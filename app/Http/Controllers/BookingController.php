<?php

namespace App\Http\Controllers;

use App\Mail\Otpsendnotifation;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\Bookingnotification;
use App\Models\Bookingpayment;
use App\Models\Candidate;
use App\Models\City;
use App\Models\Visadetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Session;
use App\Models\Adminpermission;
use App\Models\Expecworkcity;
use App\Models\Replacebookingcand;
use App\Models\Websiteconfig;
use App\Models\Userprofile;
use App\Models\Whatsappapi;
use App\Models\Templatecampaign;
use App\Models\Country;
use App\Jobs\SendBookingWelcome;
use App\Jobs\SendBookingArWelcome;
use App\Models\OrderStatus;
use App\Models\Bookingorderstatus;
use App\Models\User;
use App\Models\Partner;
use Illuminate\Support\Facades\Storage;
use App\Models\Bookingfilter;
use App\Models\Metanotification;
use App\Models\Metawhatsapplog;
use App\Models\Normalwhatsapplogs;
use App\Models\Metawhatsappapi;
use App\Models\Employercandidate;
use App\Models\Employer;
use App\Jobs\AutoSendMessageForOrder;
use App\Jobs\AutoSendMessageForOrderToPartner;
use App\Models\Autometanotification;

class BookingController extends Controller
{



    public function index(Request $request)
    {
        // $posts = DB::table('bookings as booking')
        //     ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
        //     ->leftjoin('users as user','booking.user_id','=','user.id')
        //     ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
        //     ->leftjoin('cities as city','profile.city_id','=','city.id')
        //     ->select('booking.id','booking.reference_no','booking.booking_date','booking.payment_status','booking.booking_status','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','profile.mobile_no','profile.photo','city.name as city')
        //     ->where('booking.status','=','0')
        //     ->orderBy('booking.id','DESC')
        //     ->get();

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $candidate = Candidate::where('status','=',1)->where('publish','=',1)->where('isdelete','=',0)->get();
        $ordstatuses = OrderStatus::where('admin_id','=',Auth::guard('admin')->user()->id)->get();
        $allOrder = Booking::count();
        $cancelOrder = Booking::where('booking_status','=',2)->count();
        $newOrder = Booking::where('booking_status','=',0)->count();
        $todayOrder = Booking::where('booking_date','=',date('Y-m-d'))->count();
        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $posts = DB::table('bookings as booking')
            ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
            ->leftjoin('users as user','booking.user_id','=','user.id')
            // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
            ->leftjoin('cities as city','user.city_id','=','city.id')
            ->leftjoin('professions as proff','cand.jobtype_id','=','proff.id')
            ->leftjoin('expecworkcities as expwc','expwc.id','=','booking.worklocation')
            ->leftjoin('partners as partner','partner.id','=','booking.partner_id')
            ->leftJoin('admins as canceladmin','canceladmin.id','=','booking.booking_cancelled_by')
            ->select('booking.id','booking.booking_cancelled_at','booking.booking_cancelled_by','canceladmin.name as cancelled_by_name','canceladmin.name as cancelled_by_name','partner.rec_off_name','expwc.name as expwname','proff.eng_name as pengname','proff.ar_name as pearname','booking.cand_id','cand.reference_no as candref','booking.reference_no as bookingrf_no','booking.booking_date','booking.payment_status','booking.booking_status','booking.ord_status_id','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','user.mobile_no','user.photo','user.avatar_url','city.name as city')
            // ->where('booking.booking_status','=','0')
            // ->where('booking.status','=','0')
            // ->where('booking.visa_status','=','0')
            ->orderBy('booking.id','DESC');
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_booking == 1){
                $posts = DB::table('bookings as booking')
                ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
                ->leftjoin('users as user','booking.user_id','=','user.id')
                // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
                ->leftjoin('cities as city','user.city_id','=','city.id')
                ->leftjoin('professions as proff','cand.jobtype_id','=','proff.id')
                ->leftjoin('expecworkcities as expwc','expwc.id','=','booking.worklocation')
                ->leftjoin('partners as partner','partner.id','=','booking.partner_id')
                ->leftJoin('admins as canceladmin','canceladmin.id','=','booking.booking_cancelled_by')
                ->select('booking.id','booking.booking_cancelled_at','booking.booking_cancelled_by','canceladmin.name as cancelled_by_name','partner.rec_off_name','expwc.name as expwname','proff.eng_name as pengname','proff.ar_name as pearname','cand.reference_no as candref','booking.cand_id','booking.reference_no as bookingrf_no','booking.booking_date','booking.payment_status','booking.booking_status','booking.ord_status_id','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','user.mobile_no','user.photo','user.avatar_url','city.name as city')
                // ->where('booking.status','=','0')
                // ->where('booking.booking_status','=','0')
                // ->where('booking.visa_status','=','0')
                ->orderBy('booking.id','DESC');
            }else{
                $posts = DB::table('bookings as booking')
                ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
                ->leftjoin('users as user','booking.user_id','=','user.id')
                // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
                ->leftjoin('cities as city','user.city_id','=','city.id')
                ->leftjoin('professions as proff','cand.jobtype_id','=','proff.id')
                ->leftjoin('expecworkcities as expwc','expwc.id','=','booking.worklocation')
                ->leftjoin('partners as partner','partner.id','=','booking.partner_id')
                ->leftJoin('admins as canceladmin','canceladmin.id','=','booking.booking_cancelled_by')
                ->select('booking.id','booking.booking_cancelled_at','booking.booking_cancelled_by','canceladmin.name as cancelled_by_name','partner.rec_off_name','expwc.name as expwname','proff.eng_name as pengname','proff.ar_name as pearname','cand.reference_no as candref','booking.cand_id','booking.reference_no as bookingrf_no','booking.booking_date','booking.payment_status','booking.booking_status','booking.ord_status_id','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','user.mobile_no','user.photo','user.avatar_url','city.name as city')
                // ->where('booking.status','=','0')
                // ->where('booking.booking_status','=','0')
                // ->where('booking.visa_status','=','0')
                ->where('cand.admin_id','=',Auth::guard('admin')->user()->id)
                ->orderBy('booking.id','DESC');
            }

        }

        // if(isset($request->candName) || $request->candName != ''){
        //     $posts->where('booking.cand_id','=',$request->candName);
        // }

        if(isset($request->partner) || $request->partner != ''){
            $posts->where('booking.partner_id','=',$request->partner);
        }

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

        if(isset($request->bookingStatus) || $request->bookingStatus != ''){
            $posts->where('booking.booking_status','=',$request->bookingStatus);

        }

        if(isset($request->paymentStatus) || $request->paymentStatus != ''){
            $posts->where('booking.payment_status','=',$request->paymentStatus);

        }

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
                $query->orwhere('user.mobile_no','like',"%".$request->search_booking."%");
                $query->orwhere('cand.cand_name','like',"%".$request->search_booking."%");
                $query->orwhere('cand.pass_no','like',"%".$request->search_booking."%");
                $query->orwhere('cand.reference_no','like',"%".$request->search_booking."%");
            });
        }

        $data = $posts->paginate(9)->withQueryString();

        $CheckVisa = Booking::where('visa_status', '=','0')->get();
        $bookingFilter = Bookingfilter::where('staff_id','=',Auth::guard('admin')->user()->id)->first();


        if($request->ajax()){
            return view('admin.booking.load',['posts' => $data,'perm' => $permission,'candidates' => $candidate,'ordstatuses' => $ordstatuses, 'CheckVisa' => $CheckVisa,'allOrder' => $allOrder,'cancelOrder' => $cancelOrder,'newOrder' => $newOrder,'todayOrder' => $todayOrder])->render();
        }

        $bookingStatusSummary = $this->buildBookingStatusSummary($permission);
        $activeBookingStatus = $request->filled('bookingStatus') ? (string) $request->bookingStatus : '';

        return view('admin.booking.index',['bookingFilter' => $bookingFilter,'posts' => $data,'perm' => $permission,'candidates' => $candidate,'ordstatuses' => $ordstatuses, 'CheckVisa' => $CheckVisa,'allOrder' => $allOrder,'cancelOrder' => $cancelOrder,'newOrder' => $newOrder,'todayOrder' => $todayOrder,'bookingStatusSummary' => $bookingStatusSummary,'activeBookingStatus' => $activeBookingStatus]);
    }

    /**
     * Global (permission-scoped, not filter-scoped) booking_status counts
     * for the status bar — same "counts stay fixed while the table
     * filters" principle used by the Todo/Leads/Testimonial summary
     * cards. One grouped aggregate query, scoped exactly like the list
     * itself already is (full_access / view_booking / cand.admin_id).
     */
    private function buildBookingStatusSummary($permission): array
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;

        $base = DB::table('bookings as booking')
            ->leftJoin('candidates as cand', 'booking.cand_id', '=', 'cand.id');

        if (!$isAdmin && !(isset($permission) && $permission->full_access == 1)) {
            if (!isset($permission) || $permission->view_booking != 1) {
                $base->where('cand.admin_id', '=', $user->id);
            }
        }

        $statusCounts = (clone $base)
            ->select('booking.booking_status', DB::raw('COUNT(*) as total'))
            ->groupBy('booking.booking_status')
            ->pluck('total', 'booking.booking_status');

        return [
            'total' => (int) $statusCounts->sum(),
            'statuses' => [
                0 => (int) ($statusCounts[0] ?? 0),
                1 => (int) ($statusCounts[1] ?? 0),
                2 => (int) ($statusCounts[2] ?? 0),
            ],
        ];
    }



    public function search(Request $request)
    {
        $searchTerm = $request->input('search_booking');
        $posts = Booking::where('pass_no', 'like', "%$searchTerm%")->paginate(9);
        return view('admin.booking.load', compact('posts'));
    }


    public function view($id)
    {
        $post = DB::table('bookings as booking')
             ->leftjoin('bookingpayments as bookingpayment','booking.id','=','bookingpayment.booking_id')
             ->leftjoin('admins as admin','bookingpayment.payconfirm_admin_id','=','admin.id')
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
                'bookingpayment.*',
                'admin.username as adminname',
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
            // ->where('booking.status','=','0')
            ->where('booking.id','=',$id)
            ->first();

            
        $total_exp = array_sum(explode(',',$post->experience));

        $expcities = Expecworkcity::wherein('id',explode(',',$post->expcity_id))->get();
        $mycity = [];
        foreach ($expcities as $expcity) {
            $mycity[] =  $expcity->name;
        }




        return view('admin.booking.view',compact('post','total_exp','mycity'));
    }

    public function store(Request $request)
    {
        // Count booking for reference
        $bkc = Booking::count();

        if($bkc > 0){
            $latestBk = Booking::latest()->first();
            $bkref = $latestBk->reference_no + 1;
        }else{
            $bkref = 800 + 1;
        }

        // Count booking as per candidate
        $cbkc = Booking::where('cand_id','=',$request->cand_id)->where('status','=',0)->count();

        // Candidate booking limit
        $cand_limit = Websiteconfig::first();

        // Check Order Received Panel is enable
        if($cand_limit->order_receieved != 0){
            // Order Received Panel
            $orderrecivepanels = DB::table('orderrecivepanels')->where('status','=',1)->get();
            $orderrecivepanelsC = $orderrecivepanels->count();
            if($orderrecivepanelsC > 0){
                // Get Booking if orderrecuser_id is not Blank
                $bookingordrec = Booking::where('orderrecuser_id','!=','')->count();
                // Get Order User ID
                if($orderrecivepanelsC > 1){
                    $getDisBkcordc = $bookingordrec / $orderrecivepanelsC;
                    if($getDisBkcordc > 0){
                        $getOrderBookID = [];
                        foreach($orderrecivepanels as $orderrecivepanel){
                            $newBookingorder = Booking::where('orderrecuser_id','=',$orderrecivepanel->staff_id)->count();
                            if($newBookingorder <= $getDisBkcordc){
                                $getOrderBookID [] = $orderrecivepanel->staff_id;
                            }
                        }
                        $ordrrUserID = $getOrderBookID[0];
                    }else{
                        $ordrrUserID = $orderrecivepanels[0]->staff_id;
                    }

                }else{
                    $ordrrUserID = $orderrecivepanels[0]->staff_id;
                }


            }else{
                $ordrrUserID = "";
            }

        }else{
            $ordrrUserID = "";
        }

        $post = new Booking();
        $post->cand_id = $request->cand_id;
        //07062024 $post->user_id = $request->user_id;
        $post->user_id = Auth::user()->id;
        $post->hoi = $request->hoi;
        $post->reference_no = $bkref;
        // $post->reference_no = $bkc + 1;
        if($ordrrUserID != ''){
            $post->orderrecuser_id = $ordrrUserID;
        }
        $post->booking_date = date('Y-m-d H:i:s');
        $post->worklocation = $request->worklocation;
        $post->embassy_id = $request->embassy_for;
        $post->save();

        // change candidate status 6/20/2023
        // $cand = Candidate::find($request->cand_id);
        // $cand->status = false;
        // $cand->save();

        // Change status as per candidate booking limit
        $total_bk_c = $cbkc + 1;

        if($cand_limit->cand_booking_limit <= $total_bk_c){
            $cand = Candidate::find($request->cand_id);
            $cand->status = false;
            $cand->save();
        }

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->headline = "Candidate booked by ".Auth::user()->name;
        $timeline->bodyMessage = "Candidate booked by ".Auth::user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-checklist";
        $timeline->user_id = Auth::user()->id;
        $timeline->save();

        // Send Booking Notification Job
        // $userphone = Userprofile::where('user_id','=',Auth::user()->id)->first();
        $userphone = User::where('id','=',Auth::user()->id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->first();

        // Candidate Details
        $candDet = Candidate::find($request->cand_id2);
        // API Details
        $api_id = $whatsappAPI->instance_id;
        $acc_token = $whatsappAPI->access_token;;
        $url_text = $whatsappAPI->api_url;
        // Template Name
        $template = Templatecampaign::where('template_name','=','Order Confirmation')->first();


        if(isset($userphone) && isset($whatsappAPI)){
            // Phone number with Country Code
            $getCont = Country::where('id','=',$userphone->country_id)->first();
            $phone = $getCont->country_code.''.$userphone->mobile_no;

            $sendURL = $url_text."?number=".$phone."&type=text&message=".urlencode($template->msg_whatsapp)."&instance_id=".$api_id."&access_token=".$acc_token;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$sendURL);
            $result = curl_exec($ch);
            curl_close($ch);
            // SendBookingWelcome::dispatch($userphone,$whatsappAPI,$candDet)->onQueue('orderSend');
        }



        // $data = 'Thank you! your booking reference no is BK800'.$post->reference_no;
        $data = 'Thank you for placing your order with us! Your order number is '.$post->reference_no;

        return response()->json($data);

    }

    public function storear(Request $request)
    {

        // Count booking for reference
        $bkc = Booking::count();

        if($bkc > 0){
            $latestBk = Booking::latest()->first();
            $bkref = $latestBk->reference_no + 1;
        }else{
            $bkref = 800 + 1;
        }

        // Count booking as per candidate
        $cbkc = Booking::where('cand_id','=',$request->cand_id)->where('status','=',0)->count();

        // Candidate booking limit
        $cand_limit = Websiteconfig::first();

        // Check Order Received Panel is enable
        if($cand_limit->order_receieved != 0){
            // Order Received Panel
            $orderrecivepanels = DB::table('orderrecivepanels')->where('status','=',1)->get();
            $orderrecivepanelsC = $orderrecivepanels->count();
            if($orderrecivepanelsC > 0){
                // Get Booking if orderrecuser_id is not Blank
                $bookingordrec = Booking::where('orderrecuser_id','!=','')->count();
                // Get Order User ID
                if($orderrecivepanelsC > 1){
                    $getDisBkcordc = $bookingordrec / $orderrecivepanelsC;
                    if($getDisBkcordc > 0){
                        $getOrderBookID = [];
                        foreach($orderrecivepanels as $orderrecivepanel){
                            $newBookingorder = Booking::where('orderrecuser_id','=',$orderrecivepanel->staff_id)->count();
                            if($newBookingorder <= $getDisBkcordc){
                                $getOrderBookID [] = $orderrecivepanel->staff_id;
                            }
                        }
                        $ordrrUserID = $getOrderBookID[0];
                    }else{
                        $ordrrUserID = $orderrecivepanels[0]->staff_id;
                    }

                }else{
                    $ordrrUserID = $orderrecivepanels[0]->staff_id;
                }


            }else{
                $ordrrUserID = "";
            }
        }else{
            $ordrrUserID = "";
        }

        session()->put('form_submitted', true);
        $post = new Booking();
        $post->cand_id = $request->cand_id;
        // $post->user_id = $request->user_id;
        $post->user_id = Auth::user()->id;
        $post->hoi = $request->hoi;
        // $post->reference_no = $bkc + 1;
        $post->reference_no = $bkref;
        if($ordrrUserID != ''){
            $post->orderrecuser_id = $ordrrUserID;
        }
        $post->booking_date = date('Y-m-d H:i:s');
        $post->worklocation = $request->worklocation;
        $post->embassy_id = $request->embassy_for;
        $post->save();

        // change candidate status 6/20/2023
        // $cand = Candidate::find($request->cand_id);
        // $cand->status = false;
        // $cand->save();

        // Change status as per candidate booking limit
        $total_bk_c = $cbkc + 1;

        if($cand_limit->cand_booking_limit <= $total_bk_c){
            $cand = Candidate::find($request->cand_id);
            $cand->status = false;
            $cand->save();
        }

        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->headline = "Candidate booked by ".Auth::user()->name;
        $timeline->bodyMessage = "Candidate booked by ".Auth::user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-checklist";
        $timeline->user_id = Auth::user()->id;
        $timeline->save();

        // $userphone = Userprofile::where('user_id','=',Auth::user()->id)->first();
        $userphone = User::where('id','=',Auth::user()->id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->first();

        if(isset($userphone) && isset($whatsappAPI)){
            $candDet = Candidate::find($request->cand_id2);
            $template = Templatecampaign::where('template_name','=','Order Confirmation')->first();
            $api_id = $whatsappAPI->instance_id;
            $acc_token = $whatsappAPI->access_token;;
            $url_text = $whatsappAPI->api_url;

            $getCont = Country::where('id','=',$userphone->country_id)->first();
            $phone = $getCont->country_code.''.$userphone->mobile_no;

            $sendURL = $url_text."?number=".$phone."&type=text&message=".urlencode($template->msg_whatsapp)."&instance_id=".$api_id."&access_token=".$acc_token;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$sendURL);
            $result =  curl_exec($ch);
            curl_close($ch);
        }

        // $data = 'Thank you! your booking reference no is '.$post->reference_no;

        // $data = "شكرًا لك! رقم مرجع الحجز الخاص بك هو BK800".$post->reference_no;
        $data = " شكرًا لك على طلبك معنا! رقم طلبك هو".$post->reference_no;

        return response()->json($data);
    }
    public function store2(Request $request){

        // Count booking for reference
        $bkc = Booking::count();

        if($bkc > 0){
            $latestBk = Booking::latest()->first();
            $bkref = $latestBk->reference_no + 1;
        }else{
            $bkref = 800 + 1;
        }



        // Count booking as per candidate
        $cbkc = Booking::where('cand_id','=',$request->cand_id2)->where('status','=',0)->count();
        // Candidate booking limit
        $cand_limit = Websiteconfig::first();

        // Check Order Received Panel is enable
        if($cand_limit->order_receieved != 0){
            // Order Received Panel
            $orderrecivepanels = DB::table('orderrecivepanels')->where('status','=',1)->get();
            $orderrecivepanelsC = $orderrecivepanels->count();
            if($orderrecivepanelsC > 0){
                // Get Booking if orderrecuser_id is not Blank
                $bookingordrec = Booking::where('orderrecuser_id','!=','')->count();
                // Get Order User ID
                if($orderrecivepanelsC > 1){
                    $getDisBkcordc = $bookingordrec / $orderrecivepanelsC;
                    if($getDisBkcordc > 0){
                        $getOrderBookID = [];
                        foreach($orderrecivepanels as $orderrecivepanel){
                            $newBookingorder = Booking::where('orderrecuser_id','=',$orderrecivepanel->staff_id)->count();
                            if($newBookingorder <= $getDisBkcordc){
                                $getOrderBookID [] = $orderrecivepanel->staff_id;
                            }
                        }
                        $ordrrUserID = $getOrderBookID[0];
                    }else{
                        $ordrrUserID = $orderrecivepanels[0]->staff_id;
                    }

                }else{
                    $ordrrUserID = $orderrecivepanels[0]->staff_id;
                }


            }else{
                $ordrrUserID = "";
            }

        }else{
            $ordrrUserID = "";
        }

        // Store Order into Database
        $post = new Booking();
        $post->cand_id = $request->cand_id2;
        // $post->user_id = $request->user_id2;
        $post->user_id = Auth::user()->id;
        $post->partner_id = $request->partner_id2;
        $post->reference_no = $bkref;
        // $post->reference_no = $bkc + 1;
        $post->ord_status_id = '1';
        if($ordrrUserID != ''){
            $post->orderrecuser_id = $ordrrUserID;
        }
        $post->booking_date = date('Y-m-d H:i:s');
        $post->worklocation = $request->worklocation2;
        $post->embassy_id = $request->embassy_for2;
        $post->save();

        // change candidate status 6/20/2023
        // $cand = Candidate::find($request->cand_id2);
        // $cand->status = false;
        // $cand->save();

        $total_bk_c = $cbkc + 1;

        if($cand_limit->cand_booking_limit <= $total_bk_c){
            $cand = Candidate::find($request->cand_id2);
            $cand->status = false;
            $cand->save();
        }


        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id2;
        $timeline->headline = "Candidate booked by ".Auth::user()->name;
        $timeline->bodyMessage = "Candidate booked by ".Auth::user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-checklist";
        $timeline->user_id = Auth::user()->id;
        $timeline->save();

        // Send Booking Notification Job
        // $userphone = Userprofile::where('user_id','=',Auth::user()->id)->first();
        $userphone = User::where('id','=',Auth::user()->id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $whatsappPartners = Partner::find($request->partner_id2);
        $candDetails= Candidate::find($request->cand_id2);
        // Template Name
        $template = Templatecampaign::where('template_name','=','Order Confirmation')->first();
        $template2 = Templatecampaign::where('template_name','=','Partner message for resume')->first();
        // $bookingDetails = Booking::where('user_id','=',Auth::user()->id)->latest()->first();

        $getCity = City::find($userphone->city_id);
        if(isset($getCity)){
            $city_name = $getCity->name;
        }else{
            $city2name = Expecworkcity::find($request->worklocation2);
            $city_name = $city2name->name;
        }


        // Send Notification Based on Meta Template
        $metaTemplate = Metanotification::where('meta_template_name','=','order')->where('status','=',1)->first();
        $staffMetaTemplate = Metanotification::where('meta_template_name','=','orderto_staff_partner')->where('status','=',1)->first();

        if(isset($userphone) && isset($metaTemplate)){
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
                    $data[$field_var[$i]] = $post->reference_no;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_1") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                }
                if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                    $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                }

                if ($assign_var[$i] == "partner_staff_concern_name_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                }

                if ($assign_var[$i] == "partner_address_english") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                }
                if ($assign_var[$i] == "partner_name") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_name;
                }
                if ($assign_var[$i] == "partner_mobile_no") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                }
                if ($assign_var[$i] == "rec_office_name_eng") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                }
                if ($assign_var[$i] == "rec_office_name_ar") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                }
                if ($assign_var[$i] == "booking_date") {
                    $data[$field_var[$i]] = $post->booking_date;
                }
                if ($assign_var[$i] == "candidate_name") {
                    $data[$field_var[$i]] = $candDetails->cand_name;
                }
                if ($assign_var[$i] == "candidate_contact_no") {
                    $data[$field_var[$i]] = $candDetails->mobile_no;
                }
                if ($assign_var[$i] == "customer_phone_no") {
                    $data[$field_var[$i]] = $userphone->mobile_no;
                }
                if ($assign_var[$i] == "location") {
                    $data[$field_var[$i]] = $city_name;
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
            // Store Response Message on Meta Logs

            if (isset($responseGet->errors)) {
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Booking";
                $postLog->message_type = "Template";
                $postLog->template_name = $metaTemplate->meta_template_name;
                $postLog->message_status = "failed";
                $postLog->message_text = $responseGet->message;
                $postLog->message_data_error = json_encode($responseGet->errors);
                $postLog->save();

                // Send To Normal Whatsapp
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
                        '[order_reference_number]' => $post->reference_no,
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
                        '[booking_date]' => $post->booking_date,
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
                    curl_close($curl);
                    // Store normal whatsapp response into logs
                    $normalResult = json_decode($result);

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
            } else {
                if ($responseGet->result == 'success') {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Booking";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $metaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();
                }else{
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Booking";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $metaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                    // Send To Normal Whatsapp
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
                            '[order_reference_number]' => $post->reference_no,
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
                            '[booking_date]' => $post->booking_date,
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
                        curl_close($curl);
                        // Store normal whatsapp response into logs
                        $normalResult = json_decode($result);

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
                }
            }

        }

        if(isset($whatsappPartners) && isset($staffMetaTemplate)){
            $field_var = explode(",",$staffMetaTemplate->meta_field_name);
            $assign_var = explode(",",$staffMetaTemplate->assign_var_name);


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


            if($whatsappPartners->portal_mobile_no_1 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_1;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name1,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs

                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template2->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template2->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }


            }

            if($whatsappPartners->portal_mobile_no_2 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_2;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name2,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];




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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template2->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template2->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }

            }
            if($whatsappPartners->portal_mobile_no_3 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_3;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name3,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template2->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template2->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }

            }

            if($whatsappPartners->portal_mobile_no_4 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_4;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name4,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template2->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template2->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }

            }


        }


        // $data = 'Thank you! your booking reference no is BK800'.$post->reference_no;
        $data = 'Thank you for placing your order with us! Your order number is '.$post->reference_no;
        return response()->json($data);
    }
    public function store3(Request $request){
        // Count booking for reference
        $bkc = Booking::count();

        if($bkc > 0){
            $latestBk = Booking::latest()->first();
            $bkref = $latestBk->reference_no + 1;
        }else{
            $bkref = 800 + 1;
        }

        // Count booking as per candidate
        $cbkc = Booking::where('cand_id','=',$request->cand_id3)->where('status','=',0)->count();
        // Candidate booking limit
        $cand_limit = Websiteconfig::first();
        // Check Order Received Panel is enable
        if($cand_limit->order_receieved != 0){
            // Order Received Panel
            $orderrecivepanels = DB::table('orderrecivepanels')->where('status','=',1)->get();
            $orderrecivepanelsC = $orderrecivepanels->count();
            if($orderrecivepanelsC > 0){
                // Get Booking if orderrecuser_id is not Blank
                $bookingordrec = Booking::where('orderrecuser_id','!=','')->count();
                // Get Order User ID
                if($orderrecivepanelsC > 1){
                    $getDisBkcordc = $bookingordrec / $orderrecivepanelsC;
                    if($getDisBkcordc > 0){
                        $getOrderBookID = [];
                        foreach($orderrecivepanels as $orderrecivepanel){
                            $newBookingorder = Booking::where('orderrecuser_id','=',$orderrecivepanel->staff_id)->count();
                            if($newBookingorder <= $getDisBkcordc){
                                $getOrderBookID [] = $orderrecivepanel->staff_id;
                            }
                        }
                        $ordrrUserID = $getOrderBookID[0];
                    }else{
                        $ordrrUserID = $orderrecivepanels[0]->staff_id;
                    }

                }else{
                    $ordrrUserID = $orderrecivepanels[0]->staff_id;
                }


            }else{
                $ordrrUserID = "";
            }

        }else{
            $ordrrUserID = "";
        }


        $post = new Booking();
        $post->cand_id = $request->cand_id3;
        // $post->user_id = $request->user_id3;
        $post->user_id = Auth::user()->id;
        $post->partner_id = $request->partner_id3;
        $post->reference_no = $bkref;
        // $post->reference_no = $bkc + 1;
        $post->ord_status_id = '1';
        if($ordrrUserID != ''){
            $post->orderrecuser_id = $ordrrUserID;
        }
        $post->booking_date = date('Y-m-d H:i:s');
        $post->worklocation = $request->worklocation3;
        $post->embassy_id = $request->embassy_for3;
        $post->save();

        $total_bk_c = $cbkc + 1;

        if($cand_limit->cand_booking_limit <= $total_bk_c){
            $cand = Candidate::find($request->cand_id3);
            $cand->status = false;
            $cand->save();
        }


        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id3;
        $timeline->headline = "Candidate booked by ".Auth::user()->name;
        $timeline->bodyMessage = "Candidate booked by ".Auth::user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-checklist";
        $timeline->user_id = Auth::user()->id;
        $timeline->save();

        // Send Booking Notification Job
        // Update 02042024
        // $userphone = Userprofile::where('user_id','=',Auth::user()->id)->first();
        $userphone = User::where('id','=',Auth::user()->id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $candDetails= Candidate::find($request->cand_id3);
        $whatsappPartners = Partner::find($request->partner_id3);
        $template = Templatecampaign::where('template_name','=','Order Confirmation')->first();
        $bookingDetails = Booking::where('user_id','=',Auth::user()->id)->latest()->first();
        $template3 = Templatecampaign::where('template_name','=','Partner message for resume')->first();

        $getCity = City::find($userphone->city_id);
        if(isset($getCity)){
            $city_name = $getCity->name;
        }else{
            $city2name = Expecworkcity::find($request->worklocation3);
            $city_name = $city2name->name;
        }



        // Send Notification Based on Meta Template
        $metaTemplate = Metanotification::where('meta_template_name','=','order')->where('status','=',1)->first();
        $staffMetaTemplate = Metanotification::where('meta_template_name','=','orderto_staff_partner')->where('status','=',1)->first();


        if(isset($userphone) && isset($metaTemplate)){
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
                    $data[$field_var[$i]] = $post->reference_no;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_1") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                }
                if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                    $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                }

                if ($assign_var[$i] == "partner_staff_concern_name_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                }

                if ($assign_var[$i] == "partner_address_english") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                }
                if ($assign_var[$i] == "partner_name") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_name;
                }
                if ($assign_var[$i] == "partner_mobile_no") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                }
                if ($assign_var[$i] == "rec_office_name_eng") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                }
                if ($assign_var[$i] == "rec_office_name_ar") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                }
                if ($assign_var[$i] == "booking_date") {
                    $data[$field_var[$i]] = $post->booking_date;
                }
                if ($assign_var[$i] == "candidate_name") {
                    $data[$field_var[$i]] = $candDetails->cand_name;
                }
                if ($assign_var[$i] == "candidate_contact_no") {
                    $data[$field_var[$i]] = $candDetails->mobile_no;
                }
                if ($assign_var[$i] == "customer_phone_no") {
                    $data[$field_var[$i]] = $userphone->mobile_no;
                }
                if ($assign_var[$i] == "location") {
                    $data[$field_var[$i]] = $city_name;
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
            // Store Response Message on Meta Logs
            if (isset($responseGet->errors)) {
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Booking";
                $postLog->message_type = "Template";
                $postLog->template_name = $metaTemplate->meta_template_name;
                $postLog->message_status = "failed";
                $postLog->message_text = $responseGet->message;
                $postLog->message_data_error = json_encode($responseGet->errors);
                $postLog->save();

                // Send To Normal Whatsapp
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
                        '[order_reference_number]' => $post->reference_no,
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
                        '[booking_date]' => $post->booking_date,
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
                    curl_close($curl);
                    // Store normal whatsapp response into logs
                    $normalResult = json_decode($result);

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
            } else {
                if ($responseGet->result == 'success') {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Booking";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $metaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();
                }else{
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Booking";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $metaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                    // Send To Normal Whatsapp
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
                            '[order_reference_number]' => $post->reference_no,
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
                            '[booking_date]' => $post->booking_date,
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
                        curl_close($curl);
                        // Store normal whatsapp response into logs
                        $normalResult = json_decode($result);

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
                }
            }


        }

        if(isset($whatsappPartners) && isset($staffMetaTemplate)){
            $field_var = explode(",",$staffMetaTemplate->meta_field_name);
            $assign_var = explode(",",$staffMetaTemplate->assign_var_name);

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


            if($whatsappPartners->portal_mobile_no_1 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_1;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name1,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template3->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template3->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }



            }
            if($whatsappPartners->portal_mobile_no_2 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_2;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name2,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];




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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template3->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template3->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }

            }
            if($whatsappPartners->portal_mobile_no_3 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_3;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name3,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template3->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template3->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }

            }

            if($whatsappPartners->portal_mobile_no_4 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_4;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name4,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template3->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();

                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template3->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }

            }

        }





        // if(isset($whatsappPartners) && isset($whatsappAPI)){
        //     // API Details
        //     $api_id3 = $whatsappAPI->instance_id;
        //     $acc_token3 = $whatsappAPI->access_token;;
        //     $url_text3 = $whatsappAPI->api_url;

        //     $phone1 = $whatsappPartners->portal_mobile_no_1;
        //     $phone2 = $whatsappPartners->portal_mobile_no_2;
        //     $phone3 = $whatsappPartners->portal_mobile_no_3;

        //     $cv_url_path3 = "{{ asset('admin/assets/images/pdf/}}";
        //     $cv_name3 =  $candDetails->cv_file;

        //     $userDataMap1 = [
        //         '[customer_name]' => $userphone->name,
        //         '[customer_phone_no]' => $userphone->mobile_no,
        //         '[partner_name]' => $whatsappPartners->owner_name,
        //         '[candidate_name]' => $candDetails->cand_name,
        //         '[order_reference_number]' => $post->reference_no,
        //         '[partner_staff_mobile1]' => $whatsappPartners->portal_mobile_no_1_text,
        //         '[partner_staff_mobile2]' => $whatsappPartners->portal_mobile_no_2_text,
        //         '[partner_staff_mobile3]' => $whatsappPartners->portal_mobile_no_3_text,
        //         '[partner_mobile_no]' => $whatsappPartners->owner_mobile_no,
        //         '[rec_office_name_eng]' => $whatsappPartners->rec_off_name,
        //         '[rec_office_name_ar]' => $whatsappPartners->rec_office_arname,
        //         '[rec_office_address_eng]' => $whatsappPartners->info_eng_address,
        //         '[rec_office_address_ar]' => $whatsappPartners->info_ar_address,
        //         '[partner_staff_concer_name1]' => $whatsappPartners->portal_consern_person_name1,
        //         '[partner_staff_concer_name2]' => $whatsappPartners->portal_consern_person_name2,
        //         '[partner_staff_concer_name3]' => $whatsappPartners->portal_consern_person_name3,
        //         '[location]' => $city_name
        //     ];

        //     $userDataMap2 = [
        //         '[booking_date]' => $post->booking_date,
        //     ];
        //     $userDataMap3 = array_merge($userDataMap1, $userDataMap2);
        //     $otpMessage3 = $template3->msg_whatsapp;

        //     // Replace placeholders in the message with user data
        //     $finalMessage3 = str_replace(array_keys($userDataMap3), array_values($userDataMap3), $otpMessage3);

        //     if($whatsappPartners->portal_mobile_no_1 != ''){
        //    $sendURL1= $url_text3."?number=".$phone1."&type=text&message=".urlencode($finalMessage3)."&instance_id=".$api_id3."&access_token=".$acc_token3;
        //     // $sendURL1 = $url_text3."?number=".$phone1."&type=media&message=".urlencode($finalMessage3)."&media_url=".$cv_url_path3."&filename=".$cv_name3."&instance_id=".$api_id3."&access_token=".$acc_token3;
        //     $ch = curl_init();
        //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //     curl_setopt($ch,CURLOPT_URL,$sendURL1);
        //     $result =  curl_exec($ch);
        //     }

        //     if($whatsappPartners->portal_mobile_no_2 != ''){
        //     $sendURL2= $url_text3."?number=".$phone2."&type=text&message=".urlencode($finalMessage3)."&instance_id=".$api_id3."&access_token=".$acc_token3;
        //     $ch = curl_init();
        //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //     curl_setopt($ch,CURLOPT_URL,$sendURL2);
        //     $result =  curl_exec($ch);
        //     }

        //     if($whatsappPartners->portal_mobile_no_3 != ''){
        //     $sendURL3= $url_text3."?number=".$phone3."&type=text&message=".urlencode($finalMessage3)."&instance_id=".$api_id3."&access_token=".$acc_token3;
        //     $ch = curl_init();
        //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //     curl_setopt($ch,CURLOPT_URL,$sendURL3);
        //     $result =  curl_exec($ch);
        //     }
        // }



        // $data = 'Thank you! your booking reference no is BK800'.$post->reference_no;
        $data = 'Thank you for placing your order with us! Your order number is '.$post->reference_no;
        return response()->json($data);
    }

    public function store4(Request $request)
    {
        // Count booking for reference
        $bkc = Booking::count();

        if($bkc > 0){
            $latestBk = Booking::latest()->first();
            $bkref = $latestBk->reference_no + 1;
        }else{
            $bkref = 800 + 1;
        }

        // Count booking as per candidate
        $cbkc = Booking::where('cand_id','=',$request->cand_id4)->where('status','=',0)->count();
        // Candidate booking limit
        $cand_limit = Websiteconfig::first();
        // Check Order Received Panel is enable
        if($cand_limit->order_receieved != 0){
            // Order Received Panel
            $orderrecivepanels = DB::table('orderrecivepanels')->where('status','=',1)->get();
            $orderrecivepanelsC = $orderrecivepanels->count();
            if($orderrecivepanelsC > 0){
                // Get Booking if orderrecuser_id is not Blank
                $bookingordrec = Booking::where('orderrecuser_id','!=','')->count();
                // Get Order User ID
                if($orderrecivepanelsC > 1){
                    $getDisBkcordc = $bookingordrec / $orderrecivepanelsC;
                    if($getDisBkcordc > 0){
                        $getOrderBookID = [];
                        foreach($orderrecivepanels as $orderrecivepanel){
                            $newBookingorder = Booking::where('orderrecuser_id','=',$orderrecivepanel->staff_id)->count();
                            if($newBookingorder <= $getDisBkcordc){
                                $getOrderBookID [] = $orderrecivepanel->staff_id;
                            }
                        }
                        $ordrrUserID = $getOrderBookID[0];
                    }else{
                        $ordrrUserID = $orderrecivepanels[0]->staff_id;
                    }

                }else{
                    $ordrrUserID = $orderrecivepanels[0]->staff_id;
                }


            }else{
                $ordrrUserID = "";
            }

        }else{
            $ordrrUserID = "";
        }


        $post = new Booking();
        $post->cand_id = $request->cand_id4;
        $post->user_id = Auth::user()->id;
        $post->partner_id = $request->partner_id4;
        $post->reference_no = $bkref;
        $post->ord_status_id = '1';
        $post->booking_date = date('Y-m-d H:i:s');
        $post->worklocation = $request->worklocation4;
        $post->embassy_id = $request->embassy_for4;

        if($ordrrUserID != ''){
            $post->orderrecuser_id = $ordrrUserID;
        }
      
        $post->save();

        $total_bk_c = $cbkc + 1;

        if($cand_limit->cand_booking_limit <= $total_bk_c){
            $cand = Candidate::find($request->cand_id4);
            $cand->status = false;
            $cand->save();
        }


        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id4;
        $timeline->headline = "Candidate booked by ".Auth::user()->name;
        $timeline->bodyMessage = "Candidate booked by ".Auth::user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-checklist";
        $timeline->user_id = Auth::user()->id;
        $timeline->save();

        // Send Booking Notification Job
        $userphone = User::where('id','=',Auth::user()->id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $candDetails= Candidate::find($request->cand_id4);
        $whatsappPartners = Partner::find($request->partner_id4);


        // Template Name
        $template = Templatecampaign::where('template_name','=','Order Confirmation')->first();
        $bookingDetails = Booking::where('user_id','=',Auth::user()->id)->latest()->first();
        $template4 = Templatecampaign::where('template_name','=','Partner message for resume')->first();

        $getCity = City::find($userphone->city_id);
        if(isset($getCity)){
            $city_name = $getCity->name;
        }else{
            $city2name = Expecworkcity::find($request->worklocation4);
            $city_name = $city2name->name;
        }


        AutoSendMessageForOrder::dispatch($post->id)->onQueue('default');
        AutoSendMessageForOrderToPartner::dispatch($post->id)->onQueue('default');


        // $data = 'Thank you! your booking reference no is BK800'.$post->reference_no;
        $data = 'Thank you for placing your order with us! Your order number is '.$post->reference_no;
        return response()->json($data);
    }

    
    public function getDetails($id)
    {
        $booking = Booking::with('workLocation')->find($id); // ✅ added
    
        if (!$booking) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        // format date
        $booking->formatted_created_at = $booking->created_at
            ? $booking->created_at->format('d M Y, h:i A')
            : null;
    
        // optional: send location name directly
        $booking->worklocation_name = optional($booking->workLocation)->name;
    
        return response()->json($booking);
    }

    public function storear2(Request $request){
        // Count booking for reference
        $bkc = Booking::count();

        if($bkc > 0){
            $latestBk = Booking::latest()->first();
            $bkref = $latestBk->reference_no + 1;
        }else{
            $bkref = 800 + 1;
        }

        // Count booking as per candidate
        $cbkc = Booking::where('cand_id','=',$request->cand_id2)->where('status','=',0)->count();

        // Candidate booking limit
        $cand_limit = Websiteconfig::first();

        // Check Order Received Panel is enable
        if($cand_limit->order_receieved != 0){
            // Order Received Panel
            $orderrecivepanels = DB::table('orderrecivepanels')->where('status','=',1)->get();
            $orderrecivepanelsC = $orderrecivepanels->count();
            if($orderrecivepanelsC > 0){
                // Get Booking if orderrecuser_id is not Blank
                $bookingordrec = Booking::where('orderrecuser_id','!=','')->count();
                // Get Order User ID
                if($orderrecivepanelsC > 1){
                    $getDisBkcordc = $bookingordrec / $orderrecivepanelsC;
                    if($getDisBkcordc > 0){
                        $getOrderBookID = [];
                        foreach($orderrecivepanels as $orderrecivepanel){
                            $newBookingorder = Booking::where('orderrecuser_id','=',$orderrecivepanel->staff_id)->count();
                            if($newBookingorder <= $getDisBkcordc){
                                $getOrderBookID [] = $orderrecivepanel->staff_id;
                            }
                        }
                        $ordrrUserID = $getOrderBookID[0];
                    }else{
                        $ordrrUserID = $orderrecivepanels[0]->staff_id;
                    }

                }else{
                    $ordrrUserID = $orderrecivepanels[0]->staff_id;
                }


            }else{
                $ordrrUserID = "";
            }

        }else{
            $ordrrUserID = "";
        }

        $post = new Booking();
        $post->cand_id = $request->cand_id2;
        // $post->user_id = $request->user_id2;
        $post->user_id = Auth::user()->id;
        $post->partner_id = $request->partner_id2;
        // $post->reference_no = $bkc + 1;
        $post->reference_no = $bkref;
        $post->ord_status_id = '1';
        if($ordrrUserID != ''){
            $post->orderrecuser_id = $ordrrUserID;
        }
        $post->booking_date = date('Y-m-d H:i:s');
        $post->worklocation = $request->worklocation2;
        $post->embassy_id = $request->embassy_for2;
        $post->save();

        // change candidate status 6/20/2023
        // $cand = Candidate::find($request->cand_id2);
        // $cand->status = false;
        // $cand->save();

        $total_bk_c = $cbkc + 1;

        if($cand_limit->cand_booking_limit <= $total_bk_c){
            $cand = Candidate::find($request->cand_id2);
            $cand->status = false;
            $cand->save();
        }


        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id2;
        $timeline->headline = "Candidate booked by ".Auth::user()->name;
        $timeline->bodyMessage = "Candidate booked by ".Auth::user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-checklist";
        $timeline->user_id = Auth::user()->id;
        $timeline->save();

        // Send Booking Notification Job
        // $userphone = Userprofile::where('user_id','=',Auth::user()->id)->first();
        $userphone = User::where('id','=',Auth::user()->id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->first();
        // Candidate Details
        $candDet = Candidate::find($request->cand_id2);
        $whatsappPartners = Partner::find($request->partner_id2);
        // Template Name
        $template = Templatecampaign::where('template_name','=','Order Confirmation')->first();
        $template2 = Templatecampaign::where('template_name','=','Partner message for resume')->first();
        $bookingDetails = Booking::where('user_id','=',Auth::user()->id)->latest()->first();


        $getCity = City::find($userphone->city_id);
        if(isset($getCity)){
            $city_name = $getCity->name;
        }else{
            $city2name = Expecworkcity::find($request->worklocation2);
            $city_name = $city2name->name;
        }

        $metaTemplate = Metanotification::where('meta_template_name','=','order')->where('status','=',1)->first();
        $staffMetaTemplate = Metanotification::where('meta_template_name','=','orderto_staff_partner')->where('status','=',1)->first();



        if(isset($userphone) && isset($metaTemplate)){
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
                    $data[$field_var[$i]] = $post->reference_no;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_1") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                }
                if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                    $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                }

                if ($assign_var[$i] == "partner_staff_concern_name_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                }

                if ($assign_var[$i] == "partner_address_english") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                }
                if ($assign_var[$i] == "partner_name") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_name;
                }
                if ($assign_var[$i] == "partner_mobile_no") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                }
                if ($assign_var[$i] == "rec_office_name_eng") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                }
                if ($assign_var[$i] == "rec_office_name_ar") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                }
                if ($assign_var[$i] == "booking_date") {
                    $data[$field_var[$i]] = $post->booking_date;
                }
                if ($assign_var[$i] == "candidate_name") {
                    $data[$field_var[$i]] = $candDet->cand_name;
                }
                if ($assign_var[$i] == "candidate_contact_no") {
                    $data[$field_var[$i]] = $candDet->mobile_no;
                }
                if ($assign_var[$i] == "customer_phone_no") {
                    $data[$field_var[$i]] = $userphone->mobile_no;
                }
                if ($assign_var[$i] == "location") {
                    $data[$field_var[$i]] = $city_name;
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
            // Store Response Message on Meta Logs

            if (isset($responseGet->errors)) {
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Booking";
                $postLog->message_type = "Template";
                $postLog->template_name = $metaTemplate->meta_template_name;
                $postLog->message_status = "failed";
                $postLog->message_text = $responseGet->message;
                $postLog->message_data_error = json_encode($responseGet->errors);
                $postLog->save();

                // Send To Normal Whatsapp
                if(isset($whatsappAPI)){
                    // API Details
                    $api_id = $whatsappAPI->instance_id;
                    $acc_token = $whatsappAPI->access_token;;
                    $url_text = $whatsappAPI->api_url;

                    $userDataMap1 = [
                        '[customer_name]' => $userphone->name,
                        '[customer_phone_no]' => $userphone->mobile_no,
                        '[partner_name]' => $whatsappPartners->owner_name,
                        '[candidate_name]' => $candDet->cand_name,
                        '[order_reference_number]' => $post->reference_no,
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
                        '[booking_date]' => $post->booking_date,
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
                    curl_close($curl);
                    // Store normal whatsapp response into logs
                    $normalResult = json_decode($result);

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
            } else {
                if ($responseGet->result == 'success') {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Booking";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $metaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();
                }else{
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Booking";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $metaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                    // Send To Normal Whatsapp
                    if(isset($whatsappAPI)){
                        // API Details
                        $api_id = $whatsappAPI->instance_id;
                        $acc_token = $whatsappAPI->access_token;;
                        $url_text = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDet->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[booking_date]' => $post->booking_date,
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
                        curl_close($curl);
                        // Store normal whatsapp response into logs
                        $normalResult = json_decode($result);

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
                }
            }


        }

        if(isset($whatsappPartners) && isset($staffMetaTemplate)){
            $field_var = explode(",",$staffMetaTemplate->meta_field_name);
            $assign_var = explode(",",$staffMetaTemplate->assign_var_name);

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


            if($whatsappPartners->portal_mobile_no_1 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_1;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDet->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDet->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name1,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDet->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template2->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDet->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDet->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template2->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDet->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }



            }
            if($whatsappPartners->portal_mobile_no_2 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_2;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDet->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDet->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name2,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];




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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDet->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template2->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDet->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDet->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template2->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDet->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }


            }
            if($whatsappPartners->portal_mobile_no_3 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_3;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDet->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDet->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name3,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDet->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template2->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDet->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDet->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template2->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDet->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }


            }

            if($whatsappPartners->portal_mobile_no_4 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_4;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDet->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDet->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name3,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDet->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template2->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDet->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template2->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDet->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template2->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDet->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template2->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }


            }

        }



        // $data = 'Thank you! your booking reference no is '.$post->reference_no;

        // $data = "شكرًا لك! رقم مرجع الحجز الخاص بك هو BK800".$post->reference_no;
        $data = " شكرًا لك على طلبك معنا! رقم طلبك هو".$post->reference_no;
        return response()->json($data);
    }

    public function storear3(Request $request){
        // Count booking for reference
        $bkc = Booking::count();

        if($bkc > 0){
            $latestBk = Booking::latest()->first();
            $bkref = $latestBk->reference_no + 1;
        }else{
            $bkref = 800 + 1;
        }

        // Count booking as per candidate
        $cbkc = Booking::where('cand_id','=',$request->cand_id3)->where('status','=',0)->count();

        // Candidate booking limit
        $cand_limit = Websiteconfig::first();

        // Check Order Received Panel is enable
        if($cand_limit->order_receieved != 0){
            // Order Received Panel
            $orderrecivepanels = DB::table('orderrecivepanels')->where('status','=',1)->get();
            $orderrecivepanelsC = $orderrecivepanels->count();
            if($orderrecivepanelsC > 0){
                // Get Booking if orderrecuser_id is not Blank
                $bookingordrec = Booking::where('orderrecuser_id','!=','')->count();
                // Get Order User ID
                if($orderrecivepanelsC > 1){
                    $getDisBkcordc = $bookingordrec / $orderrecivepanelsC;
                    if($getDisBkcordc > 0){
                        $getOrderBookID = [];
                        foreach($orderrecivepanels as $orderrecivepanel){
                            $newBookingorder = Booking::where('orderrecuser_id','=',$orderrecivepanel->staff_id)->count();
                            if($newBookingorder <= $getDisBkcordc){
                                $getOrderBookID [] = $orderrecivepanel->staff_id;
                            }
                        }
                        $ordrrUserID = $getOrderBookID[0];
                    }else{
                        $ordrrUserID = $orderrecivepanels[0]->staff_id;
                    }

                }else{
                    $ordrrUserID = $orderrecivepanels[0]->staff_id;
                }


            }else{
                $ordrrUserID = "";
            }

        }else{
            $ordrrUserID = "";
        }

        $post = new Booking();
        $post->cand_id = $request->cand_id3;
        // $post->user_id = $request->user_id3;
        $post->user_id = Auth::user()->id;
        $post->partner_id = $request->partner_id3;
        // $post->reference_no = $bkc + 1;
        $post->reference_no = $bkref;
        $post->ord_status_id = '1';
        if($ordrrUserID != ''){
            $post->orderrecuser_id = $ordrrUserID;
        }
        $post->booking_date = date('Y-m-d H:i:s');
        $post->worklocation = $request->worklocation3;
        $post->embassy_id = $request->embassy_for3;
        $post->save();

        $total_bk_c = $cbkc + 1;

        if($cand_limit->cand_booking_limit <= $total_bk_c){
            $cand = Candidate::find($request->cand_id3);
            $cand->status = false;
            $cand->save();
        }


        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id3;
        $timeline->headline = "Candidate booked by ".Auth::user()->name;
        $timeline->bodyMessage = "Candidate booked by ".Auth::user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-checklist";
        $timeline->user_id = Auth::user()->id;
        $timeline->save();


        // Send Booking Notification Job
        // $userphone = Userprofile::where('user_id','=',Auth::user()->id)->first();
        $userphone = User::where('id','=',Auth::user()->id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $candDetails= Candidate::find($request->cand_id3);
        $whatsappPartners = Partner::find($request->partner_id3);
        // Template Name
        $template = Templatecampaign::where('template_name','=','Order Confirmation')->first();
        $bookingDetails = Booking::where('user_id','=',Auth::user()->id)->latest()->first();
        $template3 = Templatecampaign::where('template_name','=','Partner message for resume')->first();


        $getCity = City::find($userphone->city_id);
        if(isset($getCity)){
            $city_name = $getCity->name;
        }else{
            $city2name = Expecworkcity::find($request->worklocation3);
            $city_name = $city2name->name;
        }

        $metaTemplate = Metanotification::where('meta_template_name','=','order')->where('status','=',1)->first();
        $staffMetaTemplate = Metanotification::where('meta_template_name','=','orderto_staff_partner')->where('status','=',1)->first();




        if(isset($userphone) && isset($metaTemplate)){
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
                    $data[$field_var[$i]] = $post->reference_no;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_1") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                }
                if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                    $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                }

                if ($assign_var[$i] == "partner_staff_concern_name_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                }

                if ($assign_var[$i] == "partner_address_english") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                }
                if ($assign_var[$i] == "partner_name") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_name;
                }
                if ($assign_var[$i] == "partner_mobile_no") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                }
                if ($assign_var[$i] == "rec_office_name_eng") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                }
                if ($assign_var[$i] == "rec_office_name_ar") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                }
                if ($assign_var[$i] == "booking_date") {
                    $data[$field_var[$i]] = $post->booking_date;
                }
                if ($assign_var[$i] == "candidate_name") {
                    $data[$field_var[$i]] = $candDetails->cand_name;
                }
                if ($assign_var[$i] == "candidate_contact_no") {
                    $data[$field_var[$i]] = $candDetails->mobile_no;
                }
                if ($assign_var[$i] == "customer_phone_no") {
                    $data[$field_var[$i]] = $userphone->mobile_no;
                }
                if ($assign_var[$i] == "location") {
                    $data[$field_var[$i]] = $city_name;
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
            // Store Response Message on Meta Logs
            if (isset($responseGet->errors)) {
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Booking";
                $postLog->message_type = "Template";
                $postLog->template_name = $metaTemplate->meta_template_name;
                $postLog->message_status = "failed";
                $postLog->message_text = $responseGet->message;
                $postLog->message_data_error = json_encode($responseGet->errors);
                $postLog->save();

                // Send To Normal Whatsapp
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
                        '[order_reference_number]' => $post->reference_no,
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
                        '[booking_date]' => $post->booking_date,
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
                    curl_close($curl);
                    // Store normal whatsapp response into logs
                    $normalResult = json_decode($result);

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
            } else {
                if ($responseGet->result == 'success') {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Booking";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $metaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();
                }else{
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Booking";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $metaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                    // Send To Normal Whatsapp
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
                            '[order_reference_number]' => $post->reference_no,
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
                            '[booking_date]' => $post->booking_date,
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
                        curl_close($curl);
                        // Store normal whatsapp response into logs
                        $normalResult = json_decode($result);

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
                }
            }


        }

        if(isset($whatsappPartners) && isset($staffMetaTemplate)){
            $field_var = explode(",",$staffMetaTemplate->meta_field_name);
            $assign_var = explode(",",$staffMetaTemplate->assign_var_name);

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


            if($whatsappPartners->portal_mobile_no_1 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_1;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name1,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template3->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template3->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }
            }
            if($whatsappPartners->portal_mobile_no_2 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_2;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name2,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];




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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template3->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template3->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }


            }
            if($whatsappPartners->portal_mobile_no_3 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_3;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name3,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template3->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template3->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }

            }

            if($whatsappPartners->portal_mobile_no_4 != ''){
                $data = [];
                $data['phone_number'] = $whatsappPartners->portal_mobile_no_4;
                $data['template_name'] = $staffMetaTemplate->meta_template_name;
                $data['template_language'] = "en_US";

                for($i=0;count($field_var) > $i;$i++){
                    if ($assign_var[$i] == "customer_name") {
                        $data[$field_var[$i]] = $userphone->name;
                    }
                    if ($assign_var[$i] == "order_reference_number") {
                        $data[$field_var[$i]] = $post->reference_no;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_1") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                        $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                    }
                    if ($assign_var[$i] == "partner_staff_concern_name_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_name_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                    }

                    if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                    }

                    if ($assign_var[$i] == "partner_address_english") {
                        $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                    }
                    if ($assign_var[$i] == "partner_name") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_name;
                    }
                    if ($assign_var[$i] == "partner_mobile_no") {
                        $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                    }
                    if ($assign_var[$i] == "rec_office_name_eng") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                    }
                    if ($assign_var[$i] == "rec_office_name_ar") {
                        $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                    }
                    if ($assign_var[$i] == "booking_date") {
                        $data[$field_var[$i]] = $post->booking_date;
                    }
                    if ($assign_var[$i] == "candidate_name") {
                        $data[$field_var[$i]] = $candDetails->cand_name;
                    }
                    if ($assign_var[$i] == "candidate_contact_no") {
                        $data[$field_var[$i]] = $candDetails->mobile_no;
                    }
                    if ($assign_var[$i] == "customer_phone_no") {
                        $data[$field_var[$i]] = $userphone->mobile_no;
                    }
                    if ($assign_var[$i] == "location") {
                        $data[$field_var[$i]] = $city_name;
                    }

                }

                $data['contact'] =  [
                    'first_name' => $whatsappPartners->portal_consern_person_name3,
                    'last_name' => "---",
                    "email" => "----",
                    "country" => "Saudi Arabia",
                    "language_code" => "en"
                ];

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

                // Store Response Message on Meta Logs
                if (isset($responseGet->errors)) {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template3->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template3->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                } else {
                    if ($responseGet->result == 'success') {
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();
                    }else{
                        $postLog = new Metawhatsapplog();
                        $postLog->message_for = "Order Partner Staff";
                        $postLog->message_type = "Template";
                        $postLog->template_name = $staffMetaTemplate->meta_template_name;
                        $postLog->message_status = $responseGet->result;
                        $postLog->message_text = $responseGet->message;
                        $postLog->save();

                        // Send to normal whatsapp
                        if($whatsappAPI){
                            // API Details
                            $api_id2 = $whatsappAPI->instance_id;
                            $acc_token2 = $whatsappAPI->access_token;
                            $url_text2 = $whatsappAPI->api_url;

                            $userDataMap1 = [
                                '[customer_name]' => $userphone->name,
                                '[customer_phone_no]' => $userphone->mobile_no,
                                '[partner_name]' => $whatsappPartners->owner_name,
                                '[candidate_name]' => $candDetails->cand_name,
                                '[order_reference_number]' => $post->reference_no,
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
                                '[location]' => $city_name
                            ];

                            $userDataMap2 = [
                                '[booking_date]' => $post->booking_date,
                            ];
                            $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                            $otpMessage2 = $template3->msg_whatsapp;

                            // Replace placeholders in the message with user data
                            $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                            $cv_name =  $candDetails->cv_file;
                            $cvFilePath = Storage::url('pdf/' . $cv_name);
                            $cv_url_path = $cvFilePath;

                            $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                            $ch = curl_init();
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch,CURLOPT_URL,$sendURL1);
                            $result =  curl_exec($ch);

                            $normalResult = json_decode($result);
                            // Store Logs into normal whatsapp
                            if(isset($normalResult)){
                                if(isset($normalResult->status) && $normalResult->status == "error"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = $normalResult->message;
                                    $normalPostLog->save();
                                }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = $normalResult->status;
                                    $normalPostLog->message_text = json_encode($normalResult->message);
                                    $normalPostLog->save();
                                }else{
                                    $normalPostLog = new Normalwhatsapplogs();
                                    $normalPostLog->message_for = "Order Booking";
                                    $normalPostLog->message_type = "Template";
                                    $normalPostLog->template_name = $template3->template_name;
                                    $normalPostLog->message_status = "error";
                                    $normalPostLog->message_text = "Message Not Send";
                                    $normalPostLog->save();
                                }
                            }

                        }
                    }
                }

            }




        }

        // $data = 'Thank you! your booking reference no is '.$post->reference_no;

        // $data = "شكرًا لك! رقم مرجع الحجز الخاص بك هو BK800".$post->reference_no;
        $data = " شكرًا لك على طلبك معنا! رقم طلبك هو".$post->reference_no;
        return response()->json($data);
    }


    public function storear4(Request $request){
      // Count booking for reference
      $bkc = Booking::count();

      if($bkc > 0){
        $latestBk = Booking::latest()->first();
        $bkref = $latestBk->reference_no + 1;
        }else{
        $bkref = 800 + 1;
    }

      // Count booking as per candidate
      $cbkc = Booking::where('cand_id','=',$request->cand_id4)->where('status','=',0)->count();
      // Candidate booking limit
      $cand_limit = Websiteconfig::first();
      // Check Order Received Panel is enable
      if($cand_limit->order_receieved != 0){
          // Order Received Panel
          $orderrecivepanels = DB::table('orderrecivepanels')->where('status','=',1)->get();
          $orderrecivepanelsC = $orderrecivepanels->count();
          if($orderrecivepanelsC > 0){
              // Get Booking if orderrecuser_id is not Blank
              $bookingordrec = Booking::where('orderrecuser_id','!=','')->count();
              // Get Order User ID
              if($orderrecivepanelsC > 1){
                  $getDisBkcordc = $bookingordrec / $orderrecivepanelsC;
                  if($getDisBkcordc > 0){
                      $getOrderBookID = [];
                      foreach($orderrecivepanels as $orderrecivepanel){
                          $newBookingorder = Booking::where('orderrecuser_id','=',$orderrecivepanel->staff_id)->count();
                          if($newBookingorder <= $getDisBkcordc){
                              $getOrderBookID [] = $orderrecivepanel->staff_id;
                          }
                      }
                      $ordrrUserID = $getOrderBookID[0];
                  }else{
                      $ordrrUserID = $orderrecivepanels[0]->staff_id;
                  }

              }else{
                  $ordrrUserID = $orderrecivepanels[0]->staff_id;
              }


          }else{
              $ordrrUserID = "";
          }

      }else{
          $ordrrUserID = "";
      }


      $post = new Booking();
      $post->cand_id = $request->cand_id4;
    //   $post->user_id = $request->user_id4;
      $post->user_id = Auth::user()->id;
      $post->partner_id = $request->partner_id4;
    //   $post->reference_no = $bkc + 1;
    $post->reference_no = $bkref;
    $post->ord_status_id = '1';
      if($ordrrUserID != ''){
          $post->orderrecuser_id = $ordrrUserID;
      }
      $post->booking_date = date('Y-m-d H:i:s');
      $post->worklocation = $request->worklocation4;
      $post->embassy_id = $request->embassy_for4;
      $post->save();

      $total_bk_c = $cbkc + 1;

      if($cand_limit->cand_booking_limit <= $total_bk_c){
          $cand = Candidate::find($request->cand_id4);
          $cand->status = false;
          $cand->save();
      }


      // Create Timeline
      $timeline = new Activity();
      $timeline->cand_id = $request->cand_id4;
      $timeline->headline = "Candidate booked by ".Auth::user()->name;
      $timeline->bodyMessage = "Candidate booked by ".Auth::user()->name.' on '.date('d-m-Y h:i A');
      $timeline->icons = "ti ti-checklist";
      $timeline->user_id = Auth::user()->id;
      $timeline->save();

      // Send Booking Notification Job
      $userphone = User::where('id','=',Auth::user()->id)->first();
      $whatsappAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
      $candDetails= Candidate::find($request->cand_id4);
      $whatsappPartners = Partner::find($request->partner_id4);
      // Template Name
      $template = Templatecampaign::where('template_name','=','Order Confirmation')->first();
      $bookingDetails = Booking::where('user_id','=',Auth::user()->id)->latest()->first();
      $template4 = Templatecampaign::where('template_name','=','Partner message for resume')->first();
      $getCity = City::find($userphone->city_id);


    if(isset($getCity)){
        $city_name = $getCity->name;
    }else{
        $city2name = Expecworkcity::find($request->worklocation4);
        $city_name = $city2name->name;
    }

    $metaTemplate = Metanotification::where('meta_template_name','=','order')->where('status','=',1)->first();
    $staffMetaTemplate = Metanotification::where('meta_template_name','=','orderto_staff_partner')->where('status','=',1)->first();


    // if(isset($whatsappPartners) && isset($whatsappAPI)){
    //     // API Details
    //     $api_id4 = $whatsappAPI->instance_id;
    //     $acc_token4 = $whatsappAPI->access_token;;
    //     $url_text4 = $whatsappAPI->api_url;
    //     // Phone number with Country Code
    //     $getCont = Country::where('id','=',$userphone->country_id)->first();

    //     $phone1 = $whatsappPartners->portal_mobile_no_1;
    //     $phone2 = $whatsappPartners->portal_mobile_no_2;
    //     $phone3 = $whatsappPartners->portal_mobile_no_3;

    //     $cv_url_path4 = "{{ asset('admin/assets/images/pdf/}}";
    //     $cv_name4 =  $candDetails->cv_file;


    //     $userDataMap1 = [
    //         '[customer_name]' => $userphone->name,
    //         '[customer_phone_no]' => $userphone->mobile_no,
    //         '[partner_name]' => $whatsappPartners->owner_name,
    //         '[candidate_name]' => $candDetails->cand_name,
    //         '[order_reference_number]' => $post->reference_no,
    //         '[partner_staff_mobile1]' => $whatsappPartners->portal_mobile_no_1_text,
    //         '[partner_staff_mobile2]' => $whatsappPartners->portal_mobile_no_2_text,
    //         '[partner_staff_mobile3]' => $whatsappPartners->portal_mobile_no_3_text,
    //         '[partner_mobile_no]' => $whatsappPartners->owner_mobile_no,
    //         '[rec_office_name_eng]' => $whatsappPartners->rec_off_name,
    //         '[rec_office_name_ar]' => $whatsappPartners->rec_office_arname,
    //         '[rec_office_address_eng]' => $whatsappPartners->info_eng_address,
    //         '[rec_office_address_ar]' => $whatsappPartners->info_ar_address,
    //         '[partner_staff_concer_name1]' => $whatsappPartners->portal_consern_person_name1,
    //         '[partner_staff_concer_name2]' => $whatsappPartners->portal_consern_person_name2,
    //         '[partner_staff_concer_name3]' => $whatsappPartners->portal_consern_person_name3,
    //         '[location]' => $city_name
    //     ];

    //     $userDataMap2 = [
    //         '[booking_date]' => $post->booking_date,
    //     ];

    //     $userDataMap4 = array_merge($userDataMap1, $userDataMap2);
    //     $otpMessage4 = $template4->msg_whatsapp;

    //     // Replace placeholders in the message with user data
    //     $finalMessage4 = str_replace(array_keys($userDataMap4), array_values($userDataMap4), $otpMessage4);

    //     if($whatsappPartners->portal_mobile_no_1 != ''){
    //     $sendURL1= $url_text4."?number=".$phone1."&type=text&message=".urlencode($finalMessage4)."&instance_id=".$api_id4."&access_token=".$acc_token4;
    //     //   $sendURL1 = $url_text4."?number=".$phone1."&type=media&message=".urlencode($finalMessage4)."&media_url=".$cv_url_path4."&filename=".$cv_name4."&instance_id=".$api_id4."&access_token=".$acc_token4;
    //     $ch = curl_init();
    //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    //     curl_setopt($ch,CURLOPT_URL,$sendURL1);
    //     $result =  curl_exec($ch);
    //     }

    //     if($whatsappPartners->portal_mobile_no_2 != ''){
    //     $sendURL2= $url_text4."?number=".$phone2."&type=text&message=".urlencode($finalMessage4)."&instance_id=".$api_id4."&access_token=".$acc_token4;
    //     $ch = curl_init();
    //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    //     curl_setopt($ch,CURLOPT_URL,$sendURL2);
    //     $result =  curl_exec($ch);
    //     }

    //     if($whatsappPartners->portal_mobile_no_3 != ''){
    //     $sendURL3= $url_text4."?number=".$phone3."&type=text&message=".urlencode($finalMessage4)."&instance_id=".$api_id4."&access_token=".$acc_token4;
    //     $ch = curl_init();
    //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    //     curl_setopt($ch,CURLOPT_URL,$sendURL3);
    //     $result =  curl_exec($ch);
    //     }
    // }

    if(isset($userphone) && isset($metaTemplate)){
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
                $data[$field_var[$i]] = $post->reference_no;
            }
            if ($assign_var[$i] == "partner_staff_concern_name_1") {
                $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
            }
            if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
            }
            if ($assign_var[$i] == "partner_staff_concern_name_2") {
                $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
            }

            if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
            }

            if ($assign_var[$i] == "partner_staff_concern_name_3") {
                $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
            }

            if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
            }

            if ($assign_var[$i] == "partner_address_english") {
                $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
            }
            if ($assign_var[$i] == "partner_name") {
                $data[$field_var[$i]] = $whatsappPartners->owner_name;
            }
            if ($assign_var[$i] == "partner_mobile_no") {
                $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
            }
            if ($assign_var[$i] == "rec_office_name_eng") {
                $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
            }
            if ($assign_var[$i] == "rec_office_name_ar") {
                $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
            }
            if ($assign_var[$i] == "booking_date") {
                $data[$field_var[$i]] = $post->booking_date;
            }
            if ($assign_var[$i] == "candidate_name") {
                $data[$field_var[$i]] = $candDetails->cand_name;
            }
            if ($assign_var[$i] == "candidate_contact_no") {
                $data[$field_var[$i]] = $candDetails->mobile_no;
            }
            if ($assign_var[$i] == "customer_phone_no") {
                $data[$field_var[$i]] = $userphone->mobile_no;
            }
            if ($assign_var[$i] == "location") {
                $data[$field_var[$i]] = $city_name;
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
        // Store Response Message on Meta Logs
        if (isset($responseGet->errors)) {
            $postLog = new Metawhatsapplog();
            $postLog->message_for = "Order Booking";
            $postLog->message_type = "Template";
            $postLog->template_name = $metaTemplate->meta_template_name;
            $postLog->message_status = "failed";
            $postLog->message_text = $responseGet->message;
            $postLog->message_data_error = json_encode($responseGet->errors);
            $postLog->save();

            // Send To Normal Whatsapp
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
                    '[order_reference_number]' => $post->reference_no,
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
                    '[booking_date]' => $post->booking_date,
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
                curl_close($curl);
                // Store normal whatsapp response into logs
                $normalResult = json_decode($result);

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
        } else {
            if ($responseGet->result == 'success') {
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Booking";
                $postLog->message_type = "Template";
                $postLog->template_name = $metaTemplate->meta_template_name;
                $postLog->message_status = $responseGet->result;
                $postLog->message_text = $responseGet->message;
                $postLog->save();
            }else{
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Booking";
                $postLog->message_type = "Template";
                $postLog->template_name = $metaTemplate->meta_template_name;
                $postLog->message_status = $responseGet->result;
                $postLog->message_text = $responseGet->message;
                $postLog->save();

                // Send To Normal Whatsapp
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
                        '[order_reference_number]' => $post->reference_no,
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
                        '[booking_date]' => $post->booking_date,
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
                    curl_close($curl);
                    // Store normal whatsapp response into logs
                    $normalResult = json_decode($result);

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
            }
        }


    }

    if(isset($whatsappPartners) && isset($staffMetaTemplate)){
        $field_var = explode(",",$staffMetaTemplate->meta_field_name);
        $assign_var = explode(",",$staffMetaTemplate->assign_var_name);

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


        if($whatsappPartners->portal_mobile_no_1 != ''){
            $data = [];
            $data['phone_number'] = $whatsappPartners->portal_mobile_no_1;
            $data['template_name'] = $staffMetaTemplate->meta_template_name;
            $data['template_language'] = "en_US";

            for($i=0;count($field_var) > $i;$i++){
                if ($assign_var[$i] == "customer_name") {
                    $data[$field_var[$i]] = $userphone->name;
                }
                if ($assign_var[$i] == "order_reference_number") {
                    $data[$field_var[$i]] = $post->reference_no;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_1") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                }
                if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                    $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                }

                if ($assign_var[$i] == "partner_staff_concern_name_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                }

                if ($assign_var[$i] == "partner_address_english") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                }
                if ($assign_var[$i] == "partner_name") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_name;
                }
                if ($assign_var[$i] == "partner_mobile_no") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                }
                if ($assign_var[$i] == "rec_office_name_eng") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                }
                if ($assign_var[$i] == "rec_office_name_ar") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                }
                if ($assign_var[$i] == "booking_date") {
                    $data[$field_var[$i]] = $post->booking_date;
                }
                if ($assign_var[$i] == "candidate_name") {
                    $data[$field_var[$i]] = $candDetails->cand_name;
                }
                if ($assign_var[$i] == "candidate_contact_no") {
                    $data[$field_var[$i]] = $candDetails->mobile_no;
                }
                if ($assign_var[$i] == "customer_phone_no") {
                    $data[$field_var[$i]] = $userphone->mobile_no;
                }
                if ($assign_var[$i] == "location") {
                    $data[$field_var[$i]] = $city_name;
                }

            }

            $data['contact'] =  [
                'first_name' => $whatsappPartners->portal_consern_person_name1,
                'last_name' => "---",
                "email" => "----",
                "country" => "Saudi Arabia",
                "language_code" => "en"
            ];

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

            // Store Response Message on Meta Logs
            if (isset($responseGet->errors)) {
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Partner Staff";
                $postLog->message_type = "Template";
                $postLog->template_name = $staffMetaTemplate->meta_template_name;
                $postLog->message_status = "Failed";
                $postLog->message_text = $responseGet->message;
                $postLog->message_data_error = json_encode($responseGet->errors);
                $postLog->save();

                // Send to normal whatsapp
                if($whatsappAPI){
                    // API Details
                    $api_id2 = $whatsappAPI->instance_id;
                    $acc_token2 = $whatsappAPI->access_token;
                    $url_text2 = $whatsappAPI->api_url;

                    $userDataMap1 = [
                        '[customer_name]' => $userphone->name,
                        '[customer_phone_no]' => $userphone->mobile_no,
                        '[partner_name]' => $whatsappPartners->owner_name,
                        '[candidate_name]' => $candDetails->cand_name,
                        '[order_reference_number]' => $post->reference_no,
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
                        '[location]' => $city_name
                    ];

                    $userDataMap2 = [
                        '[booking_date]' => $post->booking_date,
                    ];
                    $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                    $otpMessage2 = $template4->msg_whatsapp;

                    // Replace placeholders in the message with user data
                    $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                    $cv_name =  $candDetails->cv_file;
                    $cvFilePath = Storage::url('pdf/' . $cv_name);
                    $cv_url_path = $cvFilePath;

                    $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL1);
                    $result =  curl_exec($ch);

                    $normalResult = json_decode($result);
                    // Store Logs into normal whatsapp
                    if(isset($normalResult)){
                        if(isset($normalResult->status) && $normalResult->status == "error"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = $normalResult->message;
                            $normalPostLog->save();
                        }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = json_encode($normalResult->message);
                            $normalPostLog->save();
                        }else{
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = "error";
                            $normalPostLog->message_text = "Message Not Send";
                            $normalPostLog->save();
                        }
                    }

                }
            } else {
                if ($responseGet->result == 'success') {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();
                }else{
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template4->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_1."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                }
            }



        }
        if($whatsappPartners->portal_mobile_no_2 != ''){
            $data = [];
            $data['phone_number'] = $whatsappPartners->portal_mobile_no_2;
            $data['template_name'] = $staffMetaTemplate->meta_template_name;
            $data['template_language'] = "en_US";

            for($i=0;count($field_var) > $i;$i++){
                if ($assign_var[$i] == "customer_name") {
                    $data[$field_var[$i]] = $userphone->name;
                }
                if ($assign_var[$i] == "order_reference_number") {
                    $data[$field_var[$i]] = $post->reference_no;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_1") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                }
                if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                    $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                }

                if ($assign_var[$i] == "partner_staff_concern_name_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                }

                if ($assign_var[$i] == "partner_address_english") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                }
                if ($assign_var[$i] == "partner_name") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_name;
                }
                if ($assign_var[$i] == "partner_mobile_no") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                }
                if ($assign_var[$i] == "rec_office_name_eng") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                }
                if ($assign_var[$i] == "rec_office_name_ar") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                }
                if ($assign_var[$i] == "booking_date") {
                    $data[$field_var[$i]] = $post->booking_date;
                }
                if ($assign_var[$i] == "candidate_name") {
                    $data[$field_var[$i]] = $candDetails->cand_name;
                }
                if ($assign_var[$i] == "candidate_contact_no") {
                    $data[$field_var[$i]] = $candDetails->mobile_no;
                }
                if ($assign_var[$i] == "customer_phone_no") {
                    $data[$field_var[$i]] = $userphone->mobile_no;
                }
                if ($assign_var[$i] == "location") {
                    $data[$field_var[$i]] = $city_name;
                }

            }

            $data['contact'] =  [
                'first_name' => $whatsappPartners->portal_consern_person_name2,
                'last_name' => "---",
                "email" => "----",
                "country" => "Saudi Arabia",
                "language_code" => "en"
            ];




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

            // Store Response Message on Meta Logs
            if (isset($responseGet->errors)) {
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Partner Staff";
                $postLog->message_type = "Template";
                $postLog->template_name = $staffMetaTemplate->meta_template_name;
                $postLog->message_status = "Failed";
                $postLog->message_text = $responseGet->message;
                $postLog->message_data_error = json_encode($responseGet->errors);
                $postLog->save();

                // Send to normal whatsapp
                if($whatsappAPI){
                    // API Details
                    $api_id2 = $whatsappAPI->instance_id;
                    $acc_token2 = $whatsappAPI->access_token;
                    $url_text2 = $whatsappAPI->api_url;

                    $userDataMap1 = [
                        '[customer_name]' => $userphone->name,
                        '[customer_phone_no]' => $userphone->mobile_no,
                        '[partner_name]' => $whatsappPartners->owner_name,
                        '[candidate_name]' => $candDetails->cand_name,
                        '[order_reference_number]' => $post->reference_no,
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
                        '[location]' => $city_name
                    ];

                    $userDataMap2 = [
                        '[booking_date]' => $post->booking_date,
                    ];
                    $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                    $otpMessage2 = $template4->msg_whatsapp;

                    // Replace placeholders in the message with user data
                    $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                    $cv_name =  $candDetails->cv_file;
                    $cvFilePath = Storage::url('pdf/' . $cv_name);
                    $cv_url_path = $cvFilePath;

                    $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL1);
                    $result =  curl_exec($ch);

                    $normalResult = json_decode($result);
                    // Store Logs into normal whatsapp
                    if(isset($normalResult)){
                        if(isset($normalResult->status) && $normalResult->status == "error"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = $normalResult->message;
                            $normalPostLog->save();
                        }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = json_encode($normalResult->message);
                            $normalPostLog->save();
                        }else{
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = "error";
                            $normalPostLog->message_text = "Message Not Send";
                            $normalPostLog->save();
                        }
                    }

                }
            } else {
                if ($responseGet->result == 'success') {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();
                }else{
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template4->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_2."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                }
            }

        }
        if($whatsappPartners->portal_mobile_no_3 != ''){
            $data = [];
            $data['phone_number'] = $whatsappPartners->portal_mobile_no_3;
            $data['template_name'] = $staffMetaTemplate->meta_template_name;
            $data['template_language'] = "en_US";

            for($i=0;count($field_var) > $i;$i++){
                if ($assign_var[$i] == "customer_name") {
                    $data[$field_var[$i]] = $userphone->name;
                }
                if ($assign_var[$i] == "order_reference_number") {
                    $data[$field_var[$i]] = $post->reference_no;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_1") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                }
                if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                    $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                }

                if ($assign_var[$i] == "partner_staff_concern_name_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                }

                if ($assign_var[$i] == "partner_address_english") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                }
                if ($assign_var[$i] == "partner_name") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_name;
                }
                if ($assign_var[$i] == "partner_mobile_no") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                }
                if ($assign_var[$i] == "rec_office_name_eng") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                }
                if ($assign_var[$i] == "rec_office_name_ar") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                }
                if ($assign_var[$i] == "booking_date") {
                    $data[$field_var[$i]] = $post->booking_date;
                }
                if ($assign_var[$i] == "candidate_name") {
                    $data[$field_var[$i]] = $candDetails->cand_name;
                }
                if ($assign_var[$i] == "candidate_contact_no") {
                    $data[$field_var[$i]] = $candDetails->mobile_no;
                }
                if ($assign_var[$i] == "customer_phone_no") {
                    $data[$field_var[$i]] = $userphone->mobile_no;
                }
                if ($assign_var[$i] == "location") {
                    $data[$field_var[$i]] = $city_name;
                }

            }

            $data['contact'] =  [
                'first_name' => $whatsappPartners->portal_consern_person_name3,
                'last_name' => "---",
                "email" => "----",
                "country" => "Saudi Arabia",
                "language_code" => "en"
            ];

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

            // Store Response Message on Meta Logs
            if (isset($responseGet->errors)) {
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Partner Staff";
                $postLog->message_type = "Template";
                $postLog->template_name = $staffMetaTemplate->meta_template_name;
                $postLog->message_status = "Failed";
                $postLog->message_text = $responseGet->message;
                $postLog->message_data_error = json_encode($responseGet->errors);
                $postLog->save();

                // Send to normal whatsapp
                if($whatsappAPI){
                    // API Details
                    $api_id2 = $whatsappAPI->instance_id;
                    $acc_token2 = $whatsappAPI->access_token;
                    $url_text2 = $whatsappAPI->api_url;

                    $userDataMap1 = [
                        '[customer_name]' => $userphone->name,
                        '[customer_phone_no]' => $userphone->mobile_no,
                        '[partner_name]' => $whatsappPartners->owner_name,
                        '[candidate_name]' => $candDetails->cand_name,
                        '[order_reference_number]' => $post->reference_no,
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
                        '[location]' => $city_name
                    ];

                    $userDataMap2 = [
                        '[booking_date]' => $post->booking_date,
                    ];
                    $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                    $otpMessage2 = $template4->msg_whatsapp;

                    // Replace placeholders in the message with user data
                    $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                    $cv_name =  $candDetails->cv_file;
                    $cvFilePath = Storage::url('pdf/' . $cv_name);
                    $cv_url_path = $cvFilePath;

                    $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL1);
                    $result =  curl_exec($ch);

                    $normalResult = json_decode($result);
                    // Store Logs into normal whatsapp
                    if(isset($normalResult)){
                        if(isset($normalResult->status) && $normalResult->status == "error"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = $normalResult->message;
                            $normalPostLog->save();
                        }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = json_encode($normalResult->message);
                            $normalPostLog->save();
                        }else{
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = "error";
                            $normalPostLog->message_text = "Message Not Send";
                            $normalPostLog->save();
                        }
                    }

                }
            } else {
                if ($responseGet->result == 'success') {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();
                }else{
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template4->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_3."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                }
            }

        }

        if($whatsappPartners->portal_mobile_no_4 != ''){
            $data = [];
            $data['phone_number'] = $whatsappPartners->portal_mobile_no_4;
            $data['template_name'] = $staffMetaTemplate->meta_template_name;
            $data['template_language'] = "en_US";

            for($i=0;count($field_var) > $i;$i++){
                if ($assign_var[$i] == "customer_name") {
                    $data[$field_var[$i]] = $userphone->name;
                }
                if ($assign_var[$i] == "order_reference_number") {
                    $data[$field_var[$i]] = $post->reference_no;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_1") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name1;
                }
                if ($assign_var[$i] == "partner_staff_concern_mobile_1") {
                    $data[$field_var[$i]] =  $whatsappPartners->portal_mobile_no_1_text;
                }
                if ($assign_var[$i] == "partner_staff_concern_name_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name2;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_2") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_2_text;
                }

                if ($assign_var[$i] == "partner_staff_concern_name_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_consern_person_name3;
                }

                if ($assign_var[$i] == "partner_staff_concern_mobile_3") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_mobile_no_3_text;
                }

                if ($assign_var[$i] == "partner_address_english") {
                    $data[$field_var[$i]] = $whatsappPartners->portal_add_for_cust;
                }
                if ($assign_var[$i] == "partner_name") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_name;
                }
                if ($assign_var[$i] == "partner_mobile_no") {
                    $data[$field_var[$i]] = $whatsappPartners->owner_mobile_no;
                }
                if ($assign_var[$i] == "rec_office_name_eng") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_off_name;
                }
                if ($assign_var[$i] == "rec_office_name_ar") {
                    $data[$field_var[$i]] = $whatsappPartners->rec_office_arname;
                }
                if ($assign_var[$i] == "booking_date") {
                    $data[$field_var[$i]] = $post->booking_date;
                }
                if ($assign_var[$i] == "candidate_name") {
                    $data[$field_var[$i]] = $candDetails->cand_name;
                }
                if ($assign_var[$i] == "candidate_contact_no") {
                    $data[$field_var[$i]] = $candDetails->mobile_no;
                }
                if ($assign_var[$i] == "customer_phone_no") {
                    $data[$field_var[$i]] = $userphone->mobile_no;
                }
                if ($assign_var[$i] == "location") {
                    $data[$field_var[$i]] = $city_name;
                }

            }

            $data['contact'] =  [
                'first_name' => $whatsappPartners->portal_consern_person_name3,
                'last_name' => "---",
                "email" => "----",
                "country" => "Saudi Arabia",
                "language_code" => "en"
            ];

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

            // Store Response Message on Meta Logs
            if (isset($responseGet->errors)) {
                $postLog = new Metawhatsapplog();
                $postLog->message_for = "Order Partner Staff";
                $postLog->message_type = "Template";
                $postLog->template_name = $staffMetaTemplate->meta_template_name;
                $postLog->message_status = "Failed";
                $postLog->message_text = $responseGet->message;
                $postLog->message_data_error = json_encode($responseGet->errors);
                $postLog->save();

                // Send to normal whatsapp
                if($whatsappAPI){
                    // API Details
                    $api_id2 = $whatsappAPI->instance_id;
                    $acc_token2 = $whatsappAPI->access_token;
                    $url_text2 = $whatsappAPI->api_url;

                    $userDataMap1 = [
                        '[customer_name]' => $userphone->name,
                        '[customer_phone_no]' => $userphone->mobile_no,
                        '[partner_name]' => $whatsappPartners->owner_name,
                        '[candidate_name]' => $candDetails->cand_name,
                        '[order_reference_number]' => $post->reference_no,
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
                        '[location]' => $city_name
                    ];

                    $userDataMap2 = [
                        '[booking_date]' => $post->booking_date,
                    ];
                    $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                    $otpMessage2 = $template4->msg_whatsapp;

                    // Replace placeholders in the message with user data
                    $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                    $cv_name =  $candDetails->cv_file;
                    $cvFilePath = Storage::url('pdf/' . $cv_name);
                    $cv_url_path = $cvFilePath;

                    $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL1);
                    $result =  curl_exec($ch);

                    $normalResult = json_decode($result);
                    // Store Logs into normal whatsapp
                    if(isset($normalResult)){
                        if(isset($normalResult->status) && $normalResult->status == "error"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = $normalResult->message;
                            $normalPostLog->save();
                        }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = json_encode($normalResult->message);
                            $normalPostLog->save();
                        }else{
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Order Booking";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template4->template_name;
                            $normalPostLog->message_status = "error";
                            $normalPostLog->message_text = "Message Not Send";
                            $normalPostLog->save();
                        }
                    }

                }
            } else {
                if ($responseGet->result == 'success') {
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();
                }else{
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Order Partner Staff";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $staffMetaTemplate->meta_template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                    // Send to normal whatsapp
                    if($whatsappAPI){
                        // API Details
                        $api_id2 = $whatsappAPI->instance_id;
                        $acc_token2 = $whatsappAPI->access_token;
                        $url_text2 = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[customer_name]' => $userphone->name,
                            '[customer_phone_no]' => $userphone->mobile_no,
                            '[partner_name]' => $whatsappPartners->owner_name,
                            '[candidate_name]' => $candDetails->cand_name,
                            '[order_reference_number]' => $post->reference_no,
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
                            '[location]' => $city_name
                        ];

                        $userDataMap2 = [
                            '[booking_date]' => $post->booking_date,
                        ];
                        $userDataMap_2 = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage2 = $template4->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage2 = str_replace(array_keys($userDataMap_2), array_values($userDataMap_2), $otpMessage2);

                        $cv_name =  $candDetails->cv_file;
                        $cvFilePath = Storage::url('pdf/' . $cv_name);
                        $cv_url_path = $cvFilePath;

                        $sendURL1= $url_text2."?number=".$whatsappPartners->portal_mobile_no_4."&type=text&message=".urlencode($finalMessage2)."&instance_id=".$api_id2."&access_token=".$acc_token2;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL1);
                        $result =  curl_exec($ch);

                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template4->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                }
            }

        }


    }



        // $data = 'Thank you! your booking reference no is '.$post->reference_no;

        // $data = "شكرًا لك! رقم مرجع الحجز الخاص بك هو BK800".$post->reference_no;
        $data = " شكرًا لك على طلبك معنا! رقم طلبك هو".$post->reference_no;
        return response()->json($data);
    }

    // Visa Part
    public function getVisa(Request $request)
    {
        $id = $request->id;
        $post = DB::table('bookings as booking')
            ->leftjoin('candidates as cand','cand.id','=','booking.cand_id')
            ->leftJoin('visadetails as visad','booking.id','visad.booking_id')
            ->leftJoin('users as user','booking.user_id','user.id')
            ->select('booking.id as bookingID','cand.exp_sal','booking.user_id as userID','booking.cand_id as CandID','user.name as uname','visad.*')
            ->where('booking.id','=',$id)
            ->first();

        return response()->json($post);
    }



    public function visaStr(Request $request)
    {
        $chkVisa = Visadetails::where('booking_id','=',$request->booking_id)->first();
        $chkemployer = Employer::where('booking_id','=',$request->booking_id)->first();

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
            $chkVisa->employer_ar_name = $request->employer_ar_name;
            $chkVisa->businesstype = "B2C Online";
            $chkVisa->openings = "1";
            $chkVisa->save();

            // Update in Visa Status in Booking
            $booking = Booking::find($request->booking_id);
            $booking->visa_status = true;
            if($booking->payment_status == 1){
                $booking->booking_status = 1;
                $booking->status = true;
            }
            $booking->save();

            // Change status of candidate when visa details added
            $cand_det = Candidate::find($request->cand_id);
            $cand_det->status = false;
            $cand_det->save();

            // Cancel other booking related to this candidate
            $canbooks = Booking::where('cand_id','=',$request->cand_id)->where('id','!=',$request->booking_id)->where('status','=',0)->get();
            if(isset($canbooks)){
                foreach($canbooks as $canbook){
                    $canbook->status = true;
                    $canbook->booking_status = 2;
                    $canbook->save();
                }
            }

            // Check and Update Employer

            if (isset($chkemployer)) {
                // Store in Employer Table
                $chkemployer->visadetails_id = $chkVisa->id;
                $chkemployer->employer_name = $request->employer_name;
                $chkemployer->employer_ar_name = $request->employer_ar_name;
                $chkemployer->visa_no = $request->visa_no;
                $chkemployer->id_no = $request->id_no;
                $chkemployer->proff_id = $request->proff_id;
                $chkemployer->issuing_authority = $request->issuing_authority;
                $chkemployer->wpcity_id = $request->wpcity_id;
                $chkemployer->salary = $request->salary;
                $chkemployer->businesstype = "B2C Online";
                $chkemployer->admin_id = Auth::guard('admin')->user()->id;
                $chkemployer->booking_id = $request->booking_id;
                $chkemployer->user_id = $request->user_id;
                $chkemployer->partneroffice_id = $booking->partner_id;
                $chkemployer->openings = "1";
                $chkemployer->save();

                $candempCheck = Employercandidate::where('emp2_id','=',$chkemployer->id)->first();
                if (isset($candempCheck)) {
                    $candempCheck->partneroffice_id = $chkemployer->partneroffice_id;
                    $candempCheck->emp2_id = $chkemployer->id;
                    $candempCheck->cand_id = $request->cand_id;
                    $candempCheck->proff_id = $request->proff_id;
                    $candempCheck->assignbystaff_id = Auth::guard('admin')->user()->id;
                    $candempCheck->assignbydate = date('Y-m-d');
                    $candempCheck->save();
                } else {
                    $candemp = new Employercandidate();
                    $candemp->partneroffice_id = $chkemployer->partneroffice_id;
                    $candemp->emp_id = $chkemployer->id;
                    $candemp->cand_id = $request->cand_id;
                    $candemp->proff_id = $request->proff_id;
                    $candemp->assignbystaff_id = Auth::guard('admin')->user()->id;
                    $candemp->assignbydate = date('Y-m-d');
                    $candemp->save();
                }

            } else {
                // Store in Employer Table
                $postEmployer = new Employer();
                $postEmployer->visadetails_id = $chkVisa->id;
                $postEmployer->employer_name = $request->employer_name;
                $postEmployer->employer_ar_name = $request->employer_ar_name;
                $postEmployer->visa_no = $request->visa_no;
                $postEmployer->id_no = $request->id_no;
                $postEmployer->proff_id = $request->proff_id;
                $postEmployer->issuing_authority = $request->issuing_authority;
                $postEmployer->wpcity_id = $request->wpcity_id;
                $postEmployer->salary = $request->salary;
                $postEmployer->businesstype = "B2C Online";
                $postEmployer->admin_id = Auth::guard('admin')->user()->id;
                $postEmployer->booking_id = $request->booking_id;
                $postEmployer->user_id = $request->user_id;
                $postEmployer->partneroffice_id = $booking->partner_id;
                $postEmployer->openings = "1";
                $postEmployer->save();

                // Assign candidate to Employer check and updated
                $candemp = new Employercandidate();
                $candemp->emp2_id = $postEmployer->id;
                $candemp->cand_id = $request->cand_id;
                $candemp->proff_id = $request->proff_id;
                $candemp->assignbystaff_id = Auth::guard('admin')->user()->id;
                $candemp->assignbydate = date('Y-m-d');
                $candemp->save();
            }

            // Create Timeline
            $timeline = new Activity();
            $timeline->cand_id = $booking->cand_id;
            $timeline->headline = "Candidate booking visa detail updated!";
            $timeline->bodyMessage = "Candidate booking visa detail updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-file-check";
            $timeline->admin_id = Auth::guard('admin')->user()->id;
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
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->status = true;
            $post->businesstype = "B2C Online";
            $post->employer_ar_name = $request->employer_ar_name;
            $post->openings = "1";
            $post->save();






            // Update in Visa Status in Booking
            $booking = Booking::find($request->booking_id);
            $booking->visa_status = true;
            if($booking->payment_status == 1){
                $booking->booking_status = 1;
                $booking->status = true;
            }
            $booking->save();

            // Store in Employer Table
            $postEmployer = new Employer();
            $postEmployer->visadetails_id = $post->id;
            $postEmployer->employer_name = $request->employer_name;
            $postEmployer->employer_ar_name = $request->employer_ar_name;
            $postEmployer->visa_no = $request->visa_no;
            $postEmployer->id_no = $request->id_no;
            $postEmployer->proff_id = $request->proff_id;
            $postEmployer->issuing_authority = $request->issuing_authority;
            $postEmployer->wpcity_id = $request->wpcity_id;
            $postEmployer->salary = $request->salary;
            $postEmployer->businesstype = "B2C Online";
            $postEmployer->admin_id = Auth::guard('admin')->user()->id;
            $postEmployer->booking_id = $request->booking_id;
            $postEmployer->user_id = $request->user_id;
            $postEmployer->partneroffice_id = $booking->partner_id;
            $postEmployer->openings = "1";
            $postEmployer->save();

            // Assign candidate to Employer check and updated
            $candemp = new Employercandidate();
            $candemp->partneroffice_id = $postEmployer->partneroffice_id;
            $candemp->emp2_id = $postEmployer->id;
            $candemp->cand_id = $request->cand_id;
            $candemp->proff_id = $request->proff_id;
            $candemp->assignbystaff_id = Auth::guard('admin')->user()->id;
            $candemp->assignbydate = date('Y-m-d');
            $candemp->save();


            // Change status of candidate when visa details added
            $cand_det = Candidate::find($request->cand_id);
            $cand_det->status = false;
            $cand_det->save();



            // Cancel other booking related to this candidate
            $canbooks = Booking::where('cand_id','=',$request->cand_id)->where('id','!=',$request->booking_id)->where('status','=',0)->get();
            if(isset($canbooks)){
                foreach($canbooks as $canbook){
                    $canbook->status = true;
                    $canbook->booking_status = 2;
                    $canbook->save();
                }
            }





            // Create Timeline
            $timeline = new Activity();
            $timeline->cand_id = $booking->cand_id;
            $timeline->headline = "Candidate booking visa detail updated!";
            $timeline->bodyMessage = "Candidate booking visa detail updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-file-check";
            $timeline->admin_id = Auth::guard('admin')->user()->id;
            $timeline->save();

            Session::flash('success','Visa Detail added');
        }


        return redirect()->back();
    }

    public function getorderstatus(Request $request)
    {
        $id = $request->id;
        $post = DB::table('bookings as booking')
            ->leftJoin('bookingorderstatuses as orderstatus','booking.id','=','orderstatus.booking_id')
            ->select('booking.id as bookingID','booking.user_id as userID','booking.cand_id as CandID','orderstatus.*')
            ->where('booking.id','=',$id)
            ->first();

        return response()->json($post);
    }

    public function orderstatusStr(Request $request)
    {

        $chkorderstatus = Bookingorderstatus::where('booking_id','=',$request->booking_id)->first();

        if (isset($chkorderstatus)) {
            // Create Booking payment
            $chkorderstatus->booking_id = $request->booking_id;
            $chkorderstatus->user_id = $request->user_id;
            $chkorderstatus->cand_id = $request->cand_id;
            $chkorderstatus->ord_status_id = $request->ord_status_id;
            $chkorderstatus->payconfirm_admin_id = Auth::guard('admin')->user()->id;
            $chkorderstatus->save();

            // Update payment status
            $booking = Booking::find($request->booking_id);
            $booking->payconfirm_admin_id = Auth::guard('admin')->user()->id;
            $booking->ord_status_id = $request->ord_id;
            if($booking->visa_status == 1){
                $booking->booking_status = 1;
                $booking->status = true;
            }
            $booking->save();

            // Create Timeline
            $timeline = new Activity();
            $timeline->cand_id = $booking->cand_id;
            $timeline->headline = "Candidate Order Status updated!";
            $timeline->bodyMessage = "Candidate Order Status updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-cash";
            $timeline->admin_id = Auth::guard('admin')->user()->id;
            $timeline->save();

            Session::flash('success','Booking Order Status updated!');

        } else {
            // Create Booking payment
            $post = new Bookingorderstatus();
            $post->booking_id = $request->booking_id;
            $post->user_id = $request->user_id;
            $post->cand_id = $request->cand_id;
            $post->ord_status_id = $request->ord_id;
            $post->payconfirm_admin_id = Auth::guard('admin')->user()->id;
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();

            // Update payment status
            $booking = Booking::find($request->booking_id);
            $booking->payconfirm_admin_id = Auth::guard('admin')->user()->id;
            $booking->ord_status_id = $request->ord_id;
            if($booking->visa_status == 1){
                $booking->booking_status = 1;
                $booking->status = true;
            }
            $booking->save();

                // Create Timeline
                $timeline = new Activity();
                $timeline->cand_id = $booking->cand_id;
                $timeline->headline = "Candidate Order Status updated!";
                $timeline->bodyMessage = "Candidate Order Status updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
                $timeline->icons = "ti ti-cash";
                $timeline->admin_id = Auth::guard('admin')->user()->id;
                $timeline->save();

            Session::flash('success','Booking Order Status Updated!');
        }


        return redirect()->back();
      /*   $booking = Booking::find($request->booking_id);
        $booking->ord_status_id = $request->ord_id;
        dd($booking);
        $booking->save();
        return redirect()->back(); */
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
            $chkPayment->payconfirm_admin_id = Auth::guard('admin')->user()->id;
            $chkPayment->save();

            // Update payment status
            $booking = Booking::find($request->booking_id);
            $booking->payconfirm_admin_id = Auth::guard('admin')->user()->id;
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
            $timeline->bodyMessage = "Candidate booking payment updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-cash";
            $timeline->admin_id = Auth::guard('admin')->user()->id;
            $timeline->save();

            Session::flash('success','Booking Payment updated!');

        } else {
            // Create Booking payment
            $post = new Bookingpayment();
            $post->booking_id = $request->booking_id;
            $post->user_id = $request->user_id;
            $post->cand_id = $request->cand_id;
            $post->amount = $request->amount;
            $post->payconfirm_admin_id = Auth::guard('admin')->user()->id;
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();

            // Update payment status
            $booking = Booking::find($request->booking_id);
            $booking->payconfirm_admin_id = Auth::guard('admin')->user()->id;
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
                $timeline->bodyMessage = "Candidate booking payment updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
                $timeline->icons = "ti ti-cash";
                $timeline->admin_id = Auth::guard('admin')->user()->id;
                $timeline->save();

            Session::flash('success','Booking Payment added!');
        }


        return redirect()->back();

    }

    public function cancel(Request $request)
    {
        // dd($request->all());
        // change booking and status of
        $post = Booking::find($request->booking_id);
        $post->status = true;
        $post->booking_status = 2;
        $post->booking_cancelled_by = 'Admin';
        $post->booking_cancelled_at = now();
        $post->save();

        // dd($post);
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
            $cand->status = true;
            $cand->publish = true;
            $cand->save();
        }


        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $post->cand_id;
        $timeline->headline = "Candidate booking canceled!";
        $timeline->bodyMessage = "Candidate booking canceled by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-square-x";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        Session::flash('success','Booking canceled!');


        // Send Booking cancellation Notification Job
        // $userphone = Userprofile::where('user_id','=',$post->user_id)->first();
        $userphone = User::where('id','=',$post->user_id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $whatsappPartners = Partner::find($post->partner_id);
        $candDetails= Candidate::find($post->cand_id);
        // Template Name
        $template = Templatecampaign::where('template_name','=','Cancel Order')->first();
        $metaTemplate = Metanotification::where('meta_template_name','=','Cancel Order')->first();
        $bookingDetails = Booking::find($request->booking_id);

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
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Cancel Order";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == 'success'){
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

        }
        return redirect()->back();
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
        // back to the confirm-booking modal (see index.blade.php) so it
        // can skip waiting for an OTP that will never arrive and let the
        // admin confirm the order directly instead.
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
        $booking->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $booking->cand_id;
        $timeline->headline = "Candidate booking confirm!";
        $timeline->bodyMessage = "Candidate booking confirm by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-checkbox";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        Session::flash('success','Booking Confirm');
        return redirect()->back();

    }

    public function getCandidate(Request $request){
        $post = DB::table('bookings as booking')
            ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
            ->select('booking.id as bkID','cand.id as candID','cand.cand_name')
            ->where('booking.id','=',$request->id)
            ->first();

        $data['data'] = $post;

        return response()->json($post);
    }

    public function replacecandidate(Request $request){

        // dd($request);

        $id = $request->rep_booking_id;
        $cand_id = $request->bkcand_id;
        $repcand_id = $request->repcand_id;

        // replace and candidate details
        $repcand = Candidate::find($repcand_id);
        $repcand->status = 0;
        $repcand->publish = 0;
        $repcand->save();
        $cand = Candidate::find($cand_id);
        // update candidate status
        $cand->status = 1;
        $cand->publish = 1;
        $cand->save();

        // Update in Booking
        $post = Booking::find($id);
        $post->cand_id = $repcand_id;
        $post->save();

        // Update in replace booking table
        $reppost = new Replacebookingcand();
        $reppost->booking_id = $id;
        $reppost->cand_id = $cand_id;
        $reppost->repcand_id = $repcand_id;
        $reppost->replaceby_id = Auth::guard('admin')->user()->id;
        $reppost->save();

        // Update in activities
        $repcandact = new Activity();
        $repcandact->cand_id = $cand_id;
        $repcandact->headline = "Remove from booking!";
        $repcandact->bodyMessage = "Candidate removed from booking by ".Auth::guard('admin')->user()->name;
        $repcandact->icons = "ti ti-square-x";
        $repcandact->admin_id = Auth::guard('admin')->user()->id;
        $repcandact->save();

        $candRep = new Activity();
        $candRep->cand_id = $repcand_id;
        $candRep->headline = "Assign to booking!";
        $candRep->bodyMessage = "Candidate assign to booking by ".Auth::guard('admin')->user()->name;
        $candRep->icons = "ti ti-refresh";
        $candRep->admin_id = Auth::guard('admin')->user()->id;
        $candRep->save();

        // Update Booking payment if exists
        $bkpayment = Bookingpayment::where('booking_id','=',$id)->first();
        if(isset($bkpayment)){
            $bkpayment->cand_id = $repcand_id;
            $bkpayment->save();
        }

        // Update Visa details if exists
        $visaDet = Visadetails::where('booking_id','=',$id)->first();
        if(isset($visaDet)){
            $visaDet->cand_id = $repcand_id;
            $visaDet->save();
        }


        return redirect()->back()->with('success','Candidate replaced successfully!');

    }

    public function bookingFilter(Request $request){

        $post = Bookingfilter::where('staff_id','=',Auth::guard('admin')->user()->id)->count();

        if($post > 0){
            $updateF = Bookingfilter::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

            if($request->partnerf == 1){
				$updateF->partner_filter = true;
			}else{
				$updateF->partner_filter = false;
			}

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
            $newFilter = new Bookingfilter();

            $newFilter->staff_id = Auth::guard('admin')->user()->id;

            if($request->partner_filter == 1){
				$newFilter->partner_filter = true;
			}else{
				$newFilter->partner_filter = false;
			}

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
}
