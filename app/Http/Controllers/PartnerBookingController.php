<?php

namespace App\Http\Controllers;

use App\Mail\Otpsendnotifation;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\Bookingnotification;
use App\Models\Bookingpayment;
use App\Models\City;
use App\Models\Expecworkcity;
use App\Models\Visadetails;
use App\Models\Userprofile;
use App\Models\Whatsappapi;
use App\Models\Partner;
use App\Models\Candidate;
use App\Models\Templatecampaign;
use App\Models\Country;
use App\Models\User;
use App\Models\Websiteconfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Session;
use App\Models\OrderStatus;
use App\Models\Partnerbookingfilter;
use App\Models\Metawhatsapplog;
use App\Models\Normalwhatsapplogs;
use App\Models\Metawhatsappapi;

class PartnerBookingController extends Controller
{
    public function index(Request $request)
    {

        $posts = DB::table('bookings as booking')
            ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
            ->leftjoin('users as user','booking.user_id','=','user.id')
            // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
            ->leftjoin('cities as city','user.city_id','=','city.id')
            ->leftjoin('expecworkcities as expwc','expwc.id','=','booking.worklocation')
            ->leftjoin('professions as proff','cand.jobtype_id','=','proff.id')
            ->leftjoin('order_statuses as ordStatus','ordStatus.id','=','booking.ord_status_id')
            ->select('booking.id','proff.eng_name as pengname','cand.reference_no as candref','ordStatus.ord_status','expwc.name as expwname','booking.cand_id','booking.reference_no','booking.booking_date','booking.payment_status','booking.booking_status','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','user.mobile_no','user.photo','user.avatar_url as uavatar','city.name as city')
            ->where('booking.status','=','0')
            // ->where('booking.booking_status','=','0')
            // ->where('booking.visa_status','=','0')
            ->where('booking.partner_id','=',Auth::guard('partner')->user()->partner_id);


            if(isset($request->customer) || $request->customer != ''){
                $posts->where('booking.user_id','=',$request->customer);
            }

            if(isset($request->orderNo) || $request->orderNo != ''){
                $posts->where('booking.reference_no','=',$request->orderNo);
            }

            if(isset($request->profession) || $request->profession != ''){
                $posts->where('cand.jobtype_id','=',$request->profession);
            }

            if(isset($request->orderStatus) || $request->orderStatus != ''){
                $posts->where('booking.ord_status_id','=',$request->orderStatus);

            }

            // if(isset($request->paymentStatus) || $request->paymentStatus != ''){
            //     $posts->where('booking.payment_status','=',$request->paymentStatus);

            // }

            if(isset($request->city) || $request->city != ''){
                $posts->where('booking.worklocation','=',$request->city);

            }

            if(isset($request->bookingDate) || $request->bookingDate != ''){
                $posts->where('booking.booking_date','=',$request->bookingDate);

            }


        if(isset($request->search_booking) || $request->search_booking != ''){
            $posts->where(function($query) use($request){
                $query->orwhere('booking.reference_no','like',"%".$request->search_booking."%");
                $query->orwhere('user.name','like',"%".$request->search_booking."%");
                $query->orwhere('cand.cand_name','like',"%".$request->search_booking."%");
                $query->orwhere('cand.pass_no','like',"%".$request->search_booking."%");
            });
        }

        $data = $posts->orderBy('booking.id','DESC')->paginate(9)->withQueryString();

        $CheckVisa = Booking::where('visa_status', '=','0')->get();
        $wcities = DB::table('expecworkcities')->orderBy('name')->get();
        $bookingFilter = Partnerbookingfilter::where('partner_id','=',Auth::guard('partner')->user()->partner_id)->first();
        if($request->ajax()){
            return view('partner.booking.load',['posts' => $data,'CheckVisa' => $CheckVisa,'wcities' => $wcities])->render();
        }


        return view('partner.booking.index',['posts' => $data,'CheckVisa' => $CheckVisa,'bookingFilter' => $bookingFilter,'wcities' => $wcities]);

    }

    public function show($id)
    {
        $post = DB::table('bookings as booking')
        ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
        ->leftjoin('users as user','booking.user_id','=','user.id')
        // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
        ->leftjoin('cities as city','user.city_id','=','city.id')
        ->leftjoin('countries as country','country.id','=','user.country_id')
        ->leftJoin('visadetails as visa','booking.id','=','visa.booking_id')
        ->leftjoin('expecworkcities as expwork','booking.worklocation','=','expwork.id')
        ->leftjoin('partners as partner','partner.id','=','booking.partner_orderconfirmby_id')
        ->select(
            'booking.*',
            'city.name as citname',
            'user.name as cuname',
            'cand.cand_name',
            'cand.pass_no',
            'cand.exp_sal',
            'cand.marital_status',
            'cand.experience',
            'cand.expcity_id',
            'cand.photo_file',
            'cand.reference_no as refNo',
            'user.mobile_no',
            'user.address as cuaddr',
            'visa.visa_no',
            'visa.employer_name',
            'visa.employer_ar_name',
            'visa.id_no',
            'visa.visa_no',
            'visa.issuing_authority',
            'country.name as contname',
            'expwork.name as expwname',
            'partner.owner_name as partername',
            'partner.rec_off_name as partneroffice'
            )
        ->where('booking.status','=','0')
        ->where('booking.id','=',$id)
        ->first();

        $total_exp = array_sum(explode(',',$post->experience));

        $expcities = Expecworkcity::wherein('id',explode(',',$post->expcity_id))->get();
        $mycity = [];
        foreach ($expcities as $expcity) {
            $mycity[] =  $expcity->name;
        }

        return view('partner.booking.show',['post' => $post,'total_exp' => $total_exp,'mycity' => $mycity]);
    }

    public function sendOTP(Request $request)
    {
        // find and delete previous generated OTP
        $bkns = Bookingnotification::where('booking_id','=',$request->id)->get();

        if ($bkns->count() > 0) {
            foreach ($bkns as $bkn) {
                $bkn->delete();
            }
        }

        $email_code = random_int(100000,999999);
        $whatsapp_code = random_int(100000,999999);
        $message_code = random_int(100000,999999);

        $current_date = date('Y-m-d H:i:s');
        $addMinutes = '10 minutes';
        $expired_date = date('Y-m-d H:i:s',strtotime($current_date.' +'.$addMinutes));

        $post = DB::table('bookings as booking')
            ->leftJoin('users as user','booking.user_id','=','user.id')
            // ->leftJoin('userprofiles as userPro','user.id','userPro.user_id')
            ->select('booking.user_id','booking.reference_no','user.email as emailID','user.name as cuname','user.mobile_no')
            ->where('booking.id','=',$request->id)
            ->first();

        // Upload in Notification
        $otpNot = new Bookingnotification();
        $otpNot->booking_id = $request->id;
        $otpNot->user_id = $post->user_id;
        $otpNot->email = $post->emailID;
        $otpNot->mobile = $post->mobile_no;
        $otpNot->email_code = $email_code;
        $otpNot->whatsapp_code = $whatsapp_code;
        $otpNot->message_code = $message_code;
        $otpNot->code_expired = $expired_date;
        $otpNot->save();

        $data = [
            'email' => $otpNot->email,
            'name' => $post->cuname,
            'reference' => $post->reference_no,
            'email_code' => $otpNot->email_code,
            'code_expired' => date('d-m-Y H:i:s',strtotime($otpNot->code_expired)),
        ];

        // Send Mail Notification - only when this booking actually has a
        // customer email on file. Bookings placed directly by a partner
        // through the Worker Partner Portal's "Hire Now" (user_id = 0, no
        // linked `users` row) have no customer to email a confirmation
        // code to, so Mail::to(null) would throw here. Signal that case
        // back to the confirm-booking modal so it can skip waiting for an
        // OTP that will never arrive and let the partner confirm directly.
        if (!empty($data['email'])) {
            Mail::to($data['email'])->send(new Otpsendnotifation($data));

            return 'sent';
        }

        return 'no_customer';
    }

    public function checkOTP(Request $request)
    {
        // check code expired or not
        $post = Bookingnotification::where('booking_id','=',$request->booking_id)->where('email_code','=',$request->otp)->where('code_expired','>',date('Y-m-d H:s:i'))->first();

        if (isset($post)) {
            $data = [
                'type' => 1,
                'message' => 'OTP is valid'
            ];
        }else{
            $data = [
                'type' => 2,
                'error' => 'OTP is invalid or expired'
            ];
        }

        return response()->json($data);
    }

    public function confirmOTP(Request $request)
    {
        $booking = Booking::find($request->booking_id);

        // Update Booking Status
        $booking->booking_status = true;
        $booking->partner_orderconfirmby_id = Auth::guard('partner')->user()->id;
        $booking->partner_order_confirm_date = date('Y-m-d');
        $booking->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $booking->cand_id;
        $timeline->headline = "Candidate booking confirm!";
        $timeline->bodyMessage = "Candidate booking confirm by ".Auth::guard('partner')->user()->owner_name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-checkbox";
        $timeline->partner_id = Auth::guard('partner')->user()->id;
        $timeline->save();

        // Session::flash('success','Booking Confirm');
        return redirect()->back()->with('success','Booking Confirm!');

    }

    // Visa Part
    public function getVisa(Request $request)
    {
        $id = $request->id;
        $post = DB::table('bookings as booking')
            ->leftJoin('visadetails as visad','booking.id','visad.booking_id')
            ->leftJoin('users as user','booking.user_id','user.id')
            ->select('booking.id as bookingID','booking.user_id as userID','booking.cand_id as CandID','user.name as uname','visad.*')
            ->where('booking.id','=',$id)
            ->first();

        return response()->json($post);
    }

    public function visaStr(Request $request)
    {
        $chkVisa = Visadetails::where('booking_id','=',$request->booking_id)->first();

        if (isset($chkVisa)) {
            $chkVisa->booking_id = $request->booking_id;
            $chkVisa->user_id = $request->user_id;
            $chkVisa->cand_id = $request->cand_id;
            $chkVisa->visa_no = $request->visa_no;
            $chkVisa->id_no = $request->id_no;
            $chkVisa->proff_id = $request->proff_id;
            $chkVisa->employer_name = $request->employer_name;
            $chkVisa->issuing_authority = $request->issuing_authority;
            $chkVisa->wpcity_id = $request->wpcity_id;
            $chkVisa->salary = $request->salary;
            $chkVisa->status = true;
            $chkVisa->save();

            // Update in Visa Status in Booking
            $booking = Booking::find($request->booking_id);
            $booking->visa_status = true;
            if($booking->payment_status == 1){
                $booking->booking_status = 1;
                $booking->status = true;
            }
            $booking->save();

            // Create Timeline
            $timeline = new Activity();
            $timeline->cand_id = $booking->cand_id;
            $timeline->headline = "Candidate booking visa detail updated!";
            $timeline->bodyMessage = "Candidate booking visa detail updated by ".Auth::guard('partner')->user()->owner_name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-file-check";
            $timeline->admin_id = Auth::guard('partner')->user()->id;
            $timeline->save();

            Session::flash('success','Visa Detail updated!');

        }else{
            // Add Visa Details
            $post = new Visadetails();
            $post->booking_id = $request->booking_id;
            $post->user_id = $request->user_id;
            $post->cand_id = $request->cand_id;
            $post->visa_no = $request->visa_no;
            $post->id_no = $request->id_no;
            $post->proff_id = $request->proff_id;
            $post->employer_name = $request->employer_name;
            $post->issuing_authority = $request->issuing_authority;
            $post->wpcity_id = $request->wpcity_id;
            $post->salary = $request->salary;
            // $post->admin_id = Auth::guard('admin')->user()->id;
            $post->partner_id = Auth::guard('partner')->user()->id;
            $post->status = true;
            $post->save();

            // Update in Visa Status in Booking
            $booking = Booking::find($request->booking_id);
            $booking->visa_status = true;
            if($booking->payment_status == 1){
                $booking->booking_status = 1;
                $booking->status = true;
            }
            $booking->save();

            // Create Timeline
            $timeline = new Activity();
            $timeline->cand_id = $booking->cand_id;
            $timeline->headline = "Candidate booking visa detail updated!";
            $timeline->bodyMessage = "Candidate booking visa detail updated by ".Auth::guard('partner')->user()->owner_name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-file-check";
            $timeline->admin_id = Auth::guard('partner')->user()->id;
            $timeline->save();

            Session::flash('success','Visa Detail added');
        }


        return redirect()->back();
    }

    public function getPayment(Request $request)
    {
        $id = $request->id;
        $post = DB::table('bookings as booking')
            ->leftJoin('bookingpayments as bpay','booking.id','=','bpay.booking_id')
            ->select('booking.id as bookingID','booking.user_id as userID','booking.cand_id as CandID','bpay.*')
            ->where('booking.id','=',$id)
            ->first();

        return response()->json($post);
    }

    public function paymentStr(Request $request)
    {
        $chkPayment = Bookingpayment::where('booking_id','=',$request->booking_id)->first();

        if (isset($chkPayment)) {
            // Create Booking payment
            $chkPayment->booking_id = $request->booking_id;
            $chkPayment->user_id = $request->user_id;
            $chkPayment->cand_id = $request->cand_id;
            $chkPayment->amount = $request->amount;
            $chkPayment->payconfirm_partner_id = Auth::guard('partner')->user()->id;
            $chkPayment->save();

            // Update payment status
            $booking = Booking::find($request->booking_id);
            $booking->payconfirm_partner_id = Auth::guard('partner')->user()->id;
            $booking->payment_status = true;
            if($booking->visa_status == 1){
                $booking->booking_status = 1;
                $booking->status = true;
            }
            $booking->save();

            // Create Timeline
            $timeline = new Activity();
            $timeline->cand_id = $booking->cand_id;
            $timeline->headline = "Candidate booking payment updated!";
            $timeline->bodyMessage = "Candidate booking payment updated by ".Auth::guard('partner')->user()->owner_name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-cash";
            $timeline->partner_id = Auth::guard('partner')->user()->id;
            $timeline->save();

            Session::flash('success','Booking Payment updated!');

        } else {
            // Create Booking payment
            $post = new Bookingpayment();
            $post->booking_id = $request->booking_id;
            $post->user_id = $request->user_id;
            $post->cand_id = $request->cand_id;
            $post->amount = $request->amount;
            $post->payconfirm_partner_id = Auth::guard('partner')->user()->id;
            $post->partner_id = Auth::guard('partner')->user()->id;
            $post->save();

            // Update payment status
            $booking = Booking::find($request->booking_id);
            $booking->payconfirm_partner_id = Auth::guard('partner')->user()->id;
            $booking->payment_status = true;
            if($booking->visa_status == 1){
                $booking->booking_status = 1;
                $booking->status = true;
            }
            $booking->save();

            // Create Timeline
            $timeline = new Activity();
            $timeline->cand_id = $booking->cand_id;
            $timeline->headline = "Candidate booking payment updated!";
            $timeline->bodyMessage = "Candidate booking payment updated by ".Auth::guard('partner')->user()->owner_name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-cash";
            $timeline->partner_id = Auth::guard('partner')->user()->id;
            $timeline->save();

            Session::flash('success','Booking Payment added!');
        }


        return redirect()->back();

    }

    public function cancelBooking(Request $request)
    {
        // change booking and status of
        $post = Booking::find($request->booking_id);
        $post->status = true;
        $post->booking_status = 2;
        $post->save();

        // Candidate Limit
        $cand_limit = Websiteconfig::first();

        // Count booking as per candidate
        $cbkc = Booking::where('cand_id','=',$post->cand_id)->where('status','=',0)->count();

        // Release candidate 21/06/2023
        // $cand = Candidate::find($post->cand_id);
        // $cand->status = true;
        // $cand->publish = true;
        // $cand->save();

        if($cbkc < $cand_limit->cand_booking_limit){
            $cand = Candidate::find($post->cand_id);
            // Re-shown only if the CRM reservation queue didn't hide it (reservation_lock full / hold / selected).
            if ($cand && empty($cand->reservation_lock)) {
                $cand->status = true;
                $cand->publish = true;
                $cand->save();
            }
        }


        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $post->cand_id;
        $timeline->headline = "Candidate booking canceled!";
        $timeline->bodyMessage = "Candidate booking canceled by ".$post->partner_id.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-square-x";
        $timeline->admin_id = $post->partner_id;
        $timeline->save();



        // Send Booking cancellation Notification Job
        // $userphone = Userprofile::where('user_id','=',$post->user_id)->first();
        $userphone = User::where('id','=',$post->user_id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $whatsappPartners = Partner::find($post->partner_id);
        $candDetails= Candidate::find($post->cand_id);
        $bookingDetails = Booking::where('user_id','=',$post->user_id)->latest()->first();
        // Template Name
        $template = Templatecampaign::where('template_name','=','Cancel Order')->first();

        if(isset($request->yes_send_notification)){

            if(isset($userphone) && isset($metaTemplate)){
                // Phone number with Country Code
                $getCont = Country::where('id','=',$userphone->country_id)->first();
                $phone = $getCont->country_code.''.$userphone->mobile_no;

                $field_var = explode(",",$metaTemplate->meta_field_name);
                $assign_var = explode(",",$metaTemplate->assign_var_name);

                $data = [];
                $data['phone_number'] = $phone;
                $data['template_name'] = $metaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

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

                $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['notification'])->first();

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
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $bookingDetails->reference_no,
                                '[partner_staff_mobile1]' => $whatsappPartners->portal_mobile_no_1_text,
                                '[partner_staff_mobile2]' => $whatsappPartners->portal_mobile_no_2_text,
                                '[partner_staff_mobile3]' => $whatsappPartners->portal_mobile_no_3_text,
                                '[partner_mobile_no]' => $whatsappPartners->owner_mobile_no,
                                '[rec_office_name_eng]' => $whatsappPartners->rec_off_name,
                                '[rec_office_name_ar]' => $whatsappPartners->rec_office_arname,
                                '[rec_office_address_eng]' => $whatsappPartners->info_eng_address,
                                '[rec_office_address_ar]' => $whatsappPartners->info_ar_address,
                                '[partner_staff_concer_name1]' => $whatsappPartners->portal_consern_person_name1,
                                '[partner_staff_concer_name2]' => $whatsappPartners->portal_consern_person_name2,
                                '[partner_staff_concer_name3]' => $whatsappPartners->portal_consern_person_name3,
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
                                if($normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Cancel Order";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Cancel Order";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
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
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $bookingDetails->reference_no,
                                '[partner_staff_mobile1]' => $whatsappPartners->portal_mobile_no_1_text,
                                '[partner_staff_mobile2]' => $whatsappPartners->portal_mobile_no_2_text,
                                '[partner_staff_mobile3]' => $whatsappPartners->portal_mobile_no_3_text,
                                '[partner_mobile_no]' => $whatsappPartners->owner_mobile_no,
                                '[rec_office_name_eng]' => $whatsappPartners->rec_off_name,
                                '[rec_office_name_ar]' => $whatsappPartners->rec_office_arname,
                                '[rec_office_address_eng]' => $whatsappPartners->info_eng_address,
                                '[rec_office_address_ar]' => $whatsappPartners->info_ar_address,
                                '[partner_staff_concer_name1]' => $whatsappPartners->portal_consern_person_name1,
                                '[partner_staff_concer_name2]' => $whatsappPartners->portal_consern_person_name2,
                                '[partner_staff_concer_name3]' => $whatsappPartners->portal_consern_person_name3,
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
                                if($normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Cancel Order";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Cancel Order";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
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
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $bookingDetails->reference_no,
                            '[partner_staff_mobile1]' => $whatsappPartners->portal_mobile_no_1_text,
                            '[partner_staff_mobile2]' => $whatsappPartners->portal_mobile_no_2_text,
                            '[partner_staff_mobile3]' => $whatsappPartners->portal_mobile_no_3_text,
                            '[partner_mobile_no]' => $whatsappPartners->owner_mobile_no,
                            '[rec_office_name_eng]' => $whatsappPartners->rec_off_name,
                            '[rec_office_name_ar]' => $whatsappPartners->rec_office_arname,
                            '[rec_office_address_eng]' => $whatsappPartners->info_eng_address,
                            '[rec_office_address_ar]' => $whatsappPartners->info_ar_address,
                            '[partner_staff_concer_name1]' => $whatsappPartners->portal_consern_person_name1,
                            '[partner_staff_concer_name2]' => $whatsappPartners->portal_consern_person_name2,
                            '[partner_staff_concer_name3]' => $whatsappPartners->portal_consern_person_name3,
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
                            if($normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Cancel Order";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Cancel Order";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
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

                    $userDataMap1 = [
                        '[customer_name]' => $userphone->name,
                        '[customer_phone_no]' => $userphone->mobile_no,
                        '[partner_name]' => $whatsappPartners->owner_name,
                        '[candidate_name]' => $candDetails->cand_name,
                        '[order_reference_number]' => $bookingDetails->reference_no,
                        '[partner_staff_mobile1]' => $whatsappPartners->portal_mobile_no_1_text,
                        '[partner_staff_mobile2]' => $whatsappPartners->portal_mobile_no_2_text,
                        '[partner_staff_mobile3]' => $whatsappPartners->portal_mobile_no_3_text,
                        '[partner_mobile_no]' => $whatsappPartners->owner_mobile_no,
                        '[rec_office_name_eng]' => $whatsappPartners->rec_off_name,
                        '[rec_office_name_ar]' => $whatsappPartners->rec_office_arname,
                        '[rec_office_address_eng]' => $whatsappPartners->info_eng_address,
                        '[rec_office_address_ar]' => $whatsappPartners->info_ar_address,
                        '[partner_staff_concer_name1]' => $whatsappPartners->portal_consern_person_name1,
                        '[partner_staff_concer_name2]' => $whatsappPartners->portal_consern_person_name2,
                        '[partner_staff_concer_name3]' => $whatsappPartners->portal_consern_person_name3,
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
                        if($normalResult->status == "error"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Cancel Order";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = $normalResult->message;
                            $normalPostLog->save();
                        }else{
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Cancel Order";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = json_encode($normalResult->message);
                            $normalPostLog->save();
                        }
                    }
                }
            }

        }




        if(isset($request->yes_send_notification) && isset($userphone) && $userphone->mobile_no != ''){
            // Template Name
            $template = Templatecampaign::where('template_name','=','Cancel Order')->first();

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

            $bookingDetails = Booking::where('user_id','=',$post->user_id)->latest()->first();

            $userDataMap1 = [
                '[customer_name]' => $userphone->name,
                '[customer_phone_no]' => $userphone->mobile_no,
                '[partner_name]' => $whatsappPartners->owner_name,
                '[candidate_name]' => $candDetails->cand_name,
                '[order_reference_number]' => $bookingDetails->reference_no,
                '[partner_staff_mobile1]' => $whatsappPartners->portal_mobile_no_1,
                '[partner_staff_mobile2]' => $whatsappPartners->portal_mobile_no_2,
                '[partner_staff_mobile3]' => $whatsappPartners->portal_mobile_no_3,
                '[partner_mobile_no]' => $whatsappPartners->owner_mobile_no,
                '[rec_office_name_eng]' => $whatsappPartners->rec_off_name,
                '[rec_office_name_ar]' => $whatsappPartners->rec_office_arname,
                '[rec_office_address_eng]' => $whatsappPartners->info_eng_address,
                '[rec_office_address_ar]' => $whatsappPartners->info_ar_address,
                '[partner_staff_concer_name1]' => $whatsappPartners->portal_consern_person_name1,
                '[partner_staff_concer_name2]' => $whatsappPartners->portal_consern_person_name2,
                '[partner_staff_concer_name3]' => $whatsappPartners->portal_consern_person_name3,
            ];

            $userDataMap2 = [
                '[booking_date]' => $bookingDetails->booking_date,
            ];

            $userDataMap = array_merge($userDataMap1, $userDataMap2);

            $Message = $template->msg_whatsapp;

            // Replace placeholders in the message with user data
            $finalMessage = str_replace(array_keys($userDataMap), array_values($userDataMap), $Message);
            $sendURL = $url_text."?number=".$phone."&type=text&message=".urlencode($finalMessage)."&instance_id=".$api_id."&access_token=".$acc_token;

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$sendURL);
            $result =  curl_exec($ch);

        }

        Session::flash('success','Booking canceled!');

        return redirect()->back();
    }



    public function bookingFilterPartner(Request $request){
        $post = Partnerbookingfilter::where('partner_id','=',Auth::guard('partner')->user()->partner_id)->count();

        if($post > 0){
            $updateF = Partnerbookingfilter::where('partner_id','=',Auth::guard('partner')->user()->partner_id)->first();



            if($request->customerf == 1){
				$updateF->customer_filter = true;
			}else{
				$updateF->customer_filter = false;
			}

            if($request->professionf == 1){
				$updateF->profession_filter = true;
			}else{
				$updateF->profession_filter = false;
			}

            if($request->ordernof == 1){
				$updateF->orderno_filter = true;
			}else{
				$updateF->orderno_filter = false;
			}

            if($request->orderstatusf == 1){
				$updateF->orderstatus_filter = true;
			}else{
				$updateF->orderstatus_filter = false;
			}

            if($request->paymentstatusf == 1){
				$updateF->paymentstatus_filter = true;
			}else{
				$updateF->paymentstatus_filter = false;
			}

            if($request->cityf == 1){
				$updateF->city_filter = true;
			}else{
				$updateF->city_filter = false;
			}

            if($request->bookingdatef == 1){
				$updateF->bookingdate_filter = true;
			}else{
				$updateF->bookingdate_filter = false;
			}

            $updateF->save();
    		return response()->json('success');
        }else{
            $newFilter = new Partnerbookingfilter();

            $newFilter->partner_id = Auth::guard('partner')->user()->partner_id;



            if($request->customer_filter == 1){
				$newFilter->customer_filter = true;
			}else{
				$newFilter->customer_filter = false;
			}

            if($request->orderno_filter == 1){
				$newFilter->orderno_filter = true;
			}else{
				$newFilter->orderno_filter = false;
			}

            if($request->profession_filter == 1){
				$newFilter->profession_filter = true;
			}else{
				$newFilter->profession_filter = false;
			}

            if($request->orderstatus_filter == 1){
				$newFilter->orderstatus_filter = true;
			}else{
				$newFilter->orderstatus_filter = false;
			}

            if($request->paymentstatus_filter == 1){
				$newFilter->paymentstatus_filter = true;
			}else{
				$newFilter->paymentstatus_filter = false;
			}

            if($request->city_filter == 1){
				$newFilter->city_filter = true;
			}else{
				$newFilter->city_filter = false;
			}

            if($request->bookingdatef == 1){
				$newFilter->bookingdate_filter = true;
			}else{
				$newFilter->bookingdate_filter = false;
			}

            $newFilter->save();

            return response()->json('success');
        }
    }

    public function checkvisaprofessionPartner(Request $request){
        $post = Candidate::where('jobtype_id','=',$request->proff_id)->where('id','=',$request->cand_id)->count();

        if ($post == 0) {
            $isAvailable = 'false';
        } else {
            $isAvailable = 'true';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkvisaworklocationPartner(Request $request){
        $post = Candidate::where('expwp_id','=',$request->wpcity_id)->where('id','=',$request->cand_id)->count();

        if ($post == 0) {
            $isAvailable = 'false';
        } else {
            $isAvailable = 'true';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkvisaembassyPartner(Request $request){
        if ($request->embassy == 'mumbai') {
            $embassy = 1;
        }elseif ($request->embassy == 'delhi') {
            $embassy = 2;
        }else{
            $embassy = '';
        }

        $post = Candidate::where('embassy_for','=',$embassy)->where('id','=',$request->cand_id)->count();

        if ($post == 0) {
            $isAvailable = 'false';
        } else {
            $isAvailable = 'true';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function checkvisapaymentPartner(Request $request){
        $post = Candidate::find($request->cand_id);
        $checkPost = DB::table('customercosts')->where('exp_type','=',$post->gulfexperience)->where('proff_id','=',$post->jobtype_id)->count();
        if ($checkPost == 0) {
            $isAvailable = 'false';
        } else {
            $isAvailable = 'true';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function getBookingData(Request $request){
        $post = DB::table('bookings as booking')
            ->leftJoin('partners as partner','partner.id','=','booking.partner_id')
            ->select('partner.*')
            ->where('booking.id','=',$request->id)
            ->first();

        return response()->json($post);
    }

}
