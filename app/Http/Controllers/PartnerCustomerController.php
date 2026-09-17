<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adminpermission;
use App\Models\Booking;
use App\Models\Clientfilterlist;
use App\Models\User;
use App\Models\Visadetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\City;
use App\Models\Candidate;
use App\Models\CandidateStatus;
use App\Models\Activity;
use App\Models\Admin;
use App\Models\Candidatebackupreference;
use App\Models\Candpubst;
use App\Models\Placeofissue;
use App\Models\Profession;
use App\Models\Region;
use App\Models\Carnknown;
use App\Models\Candidatefilterlist;
use App\Models\Candidatefile;
use App\Models\Bookingpayment;
use App\Models\Candidatebookinglimit;
use App\Models\Basepathstatus;
use App\Models\Education;
use App\Models\Religion;
use App\Models\Partner;
use App\Models\Companycvexecute;
use App\Models\Associates;
use App\Models\Canassocconfirmby;
use App\Models\Candsercharge;
use App\Models\Paymentcand;
use App\Models\Expecworkcity;
use App\Models\Todolabel;
use App\Models\Candidatereminder;
use App\Models\Todo;
use File;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use App\Models\Embassy;
use App\Models\CandStatus;
use App\Models\Candmedicalhistory;
use Illuminate\Support\Str;
use DataTables;
use App\Models\Candidateadminsavefilter;
use App\Models\Employercandidate;


class PartnerCustomerController extends Controller
{
    
    public function index()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('partner')->user()->id)->first();
        $filter_user = Clientfilterlist::where('admin_id','=',Auth::guard('partner')->user()->id)->first();
        // Employer ,City and Booking Status list Country List
        $country = DB::table('users')->select('country_id')->groupBy('country_id')->where('country_id','!=','')->get();
        $city = DB::table('users')->select('city_id')->groupBy('city_id')->where('city_id','!=','')->get();
        $verified = DB::table('users')->select('verification_status')->groupBy('verification_status')->get();

        $countryLists = Country::orderBy('name')->get();
        $cityLists = City::orderBy('name')->get();
        return view('admin.client.partner.index',['perm' => $permission,'filter_user' => $filter_user,'countryfs' => $country,'cityfs' => $city,'countries' => $countryLists,'cities' => $cityLists ,'verifiedfs' => $verified]);
    }

    public function indexjson(Request $request)
    {
        $partner_customer = Booking::where('partner_id','=',Auth::guard('partner')->user()->id)->select('user_id')->groupBy('user_id')->get();

        $posts = DB::table('users as user')
        ->leftJoin('countries as country','user.country_id','=','country.id')
        ->leftJoin('cities as city','user.city_id','=','city.id')
        ->select('user.*','country.name as country','country.country_code as country_code','city.name as city')
        ->whereIn('user.id',$partner_customer)
        ->get();

        $data['data'] = $posts;
        return response()->json($data);
    }


    public function edit(Request $request){
        // dd($request->id);
        $post = User::find($request->id);

        return response()->json($post);
    }

    public function view($id)
    {
        $customer = User::findOrFail($id);
    
        return response()->json([
            ...$customer->toArray(),
    
            'created_at' => $customer->created_at
                ? $customer->created_at->format('d M Y, h:i A')
                : null,
    
            'updated_at' => $customer->updated_at
                ? $customer->updated_at->format('d M Y, h:i A')
                : null,
        ]);
    }
    

    public function update(Request $request){
        $post = User::find($request->edit_id);
        $post->name = $request->name;
        $post->email = $request->email;
        $post->mobile_no = $request->mobile_no;
        $post->country_id = $request->country_id;
        $post->city_id = $request->city_id;
        $post->address = $request->address;
        $post->save();

        return redirect()->back()->with('success','Client Data updated!');
    }

    public function delete(Request $request){
        $user = User::find($request->client_id);
        $bookings = Booking::where('user_id','=',$request->client_id)->get();
        $visaDetail = Visadetails::where('user_id','=',$request->client_id)->get();

        $user->delete();
        if (isset($bookings)) {
            foreach ($bookings as $booking) {
                $booking->delete();
            }
        }

        if (isset($visaDetail)) {
            foreach ($visaDetail as $visaDetai) {
                $visaDetai->delete();
            }
        }

        return redirect()->back()->with('success','Customer and all related field delete!');
    }


    public function checkemailexist(Request $request)
    {
        $count = User::where('email', $request->email)
                    ->where('id', '!=', $request->id)
                    ->count();

        if ($count == 0) {
            return response()->json([
                'valid' => true
            ]);
        } else {
            return response()->json([
                'valid' => false
            ]);
        }
    }


    public function checkMobileExists(Request $request)
    {
        $count = User::where('mobile_no', $request->mobile_no)
                    ->where('id', '!=', $request->id)
                    ->count();

        if ($count == 0) {
            return response()->json([
                'valid' => true
            ]);
        } else {
            return response()->json([
                'valid' => false
            ]);
        }
    }

    public function candidateView($id)
    {
        // dd($id);
        $post = Candidate::find($id);
        // dd($post);
        $cand_status = CandidateStatus::all();
        $poi = Placeofissue::find($post->poi);
        $admin = Admin::find($post->admin_id);
        $expconts = Country::wherein('id',explode(',',$post->expcountry_id))->get();
        $expcities = City::wherein('id',explode(',',$post->expcity_id))->get();
        $proffs = Profession::wherein('id',explode(',',$post->proff_id))->get();
        $candcity = City::find($post->candcity_id);
        $candRegion = Region::find($post->region_id);
        $placeofbirth = City::find($post->plb_id);
        $occupation = Profession::find($post->jobtype_id);
        $educationname = Education::find($post->education_id);
        $religionname = Religion::find($post->religion_id);

        $candsc = Candsercharge::where('cand_id','=',$id)->get();

        $careoffName = Admin::find($post->careoff_id);


        $users = Admin::where('id','!=',1)->where('status',1)->orderBy('name','ASC')->get();

        $paycands = DB::table('paymentcands as paymentcand')
            ->leftJoin('admins as admin','admin.id','=','paymentcand.admin_id')
            ->select('paymentcand.*','admin.name as uname')
            ->orderBy('paymentcand.id','DESC')
            ->where('paymentcand.cand_id','=',$id)
            ->get();


           // dd($CalAmount,$ServiveAmount,$PaytotalAmount);
        $filePhotos = Candidatefile::where('cand_id','=',$id)->get();

        // Get Timeline Activity
        $timelines = Activity::where('cand_id','=',$id)->orderBy('id','DESC')->get();

        // Car Known List
        $carkns = Carnknown::wherein('id',explode(",",$post->carknown_id))->get();

        // Vehical Transmission List
        $vehtrms = DB::table('vehical_transmission')->wherein('id',explode(",",$post->vehical_transmission))->get();

        // Edit POST
        $poiss = Placeofissue::orderBy('id','DESC')->get();
        $jobtypes = Profession::orderBy('id','DESC')->get();
        $countries = Country::orderBy('id','DESC')->get();
        $cities = City::orderBy('id','DESC')->get();
        $expworkcities = Expecworkcity::orderBy('name')->get();
        $regions = Region::orderBy('name')->get();
        $nation = Country::find($post->nation_id);
        $educations = Education::orderBy('name')->get();
        $religions = Religion::orderBy('name')->get();
        // if (Auth::guard('partner')->user()->user_type != '1') {
        //     $associates = Associates::orderBy('pty_full_name')->where('careoff_id','=',Auth::guard('partner')->user()->user_id)->where('contact_verified','=','1')->where('id','!=',73)->get();
        // } else {
        //     $associates = Associates::orderBy('pty_full_name')->where('contact_verified','1')->where('status',1)->where('id','!=',73)->get();

        // }

        $associates = Associates::orderBy('pty_full_name')->where('contact_verified','1')->where('status',1)->where('id','!=',73)->get();

        $careoffs = Admin::where('status',1)->orderBy('name')->get();
        $expworklocs = Expecworkcity::orderby('name')->get();
        $expcworlocs = Expecworkcity::wherein('id',explode(",",$post->expwp_id))->get();
        $cars = Carnknown::orderBy('name','ASC')->where('status','=',1)->get();
        $embassies = Embassy::orderBy('id','DESC')->get();
        $vehtrans = DB::table('vehical_transmission')->get();

        // booking details
        $candBooks = DB::table('bookings as booking')
            ->leftJoin('users as user','booking.user_id','=','user.id')
            ->select('booking.*','user.name as uname')
            ->where('booking.cand_id','=',$id)
            ->orderBy('booking.id',"DESC")
            ->get();

        // Download Details
        $candDownloads = DB::table('downloadcvfromwebsites as downcand')
            ->leftjoin('users as user','downcand.user_id','user.id')
            ->select('downcand.*','user.name as uname')
            ->where('downcand.cand_id','=',$id)
            ->orderBy('downcand.id','DESC')
            ->get();
        // Candidate limit
        $candcount = Candidatebookinglimit::where('cand_id','=',$id)->first();
        // Candidate CV List as per company
        $partners = Partner::orderBy('rec_off_name')->where('status','=',1)->get();
        $mycont =  [];
        $myproff = [];
        $mycity =  [];
        $myexpwl = [];
        $myCarkn = [];
        $myVehtr = [];

        foreach ($expconts as $expcont) {
            $mycont[] =  $expcont->name;
        }
        foreach ($expcities as $expcity) {
            $mycity[] =  $expcity->name;
        }
        foreach ($proffs as $proff) {
            $myproff[] =  $proff->eng_name;
        }
        foreach($expcworlocs as $expcworloc){
            $myexpwl[] = $expcworloc->name;
        }

        foreach($carkns as $carkn){
            $myCarkn[] = $carkn->name;
        }

        foreach($vehtrms as $vehtrm){
            $myVehtr[] = $vehtrm->eng_name;
        }

        $activeService = Candsercharge::where('cand_id','=',$id)
                    ->where('status','=',1)
                    ->first();

        // 1️⃣ Get Service Amount
        $ServiveAmount = 0;

        if($activeService && $activeService->amount != 'None'){
            $ServiveAmount = (float) $activeService->amount;
        }

        // 2️⃣ Get Total Paid Amount
        $PaytotalAmount = 0;

        foreach($paycands as $paycand) {
            $PaytotalAmount += (float) $paycand->amount;
        }

        // 3️⃣ Calculate Balance
        $CalAmount = $ServiveAmount - $PaytotalAmount;

        // $examinDte = time();
        // $expiryDte = strtotime($post->medical_expiry_date);
        // $diffDte = $expiryDte - $examinDte;
        // $days = round($diffDte / (60 * 60 *24));
        // dd($diffDte);

        $permission = DB::table('adminpermissions')->where('staff_id','=',Auth::guard('partner')->user()->id)->first();

        $todolabels = Todolabel::orderBy('id',"DESC")->get();

        $reminders = DB::table('candidatereminders as candidatereminder')
            ->leftJoin('candidates as cand','cand.id','=','candidatereminder.candidate_id')
            ->leftJoin('admins as admin','admin.id','=','candidatereminder.admin_id')
            ->leftJoin('admins as careoff','careoff.id','=','candidatereminder.careoff_id')
            ->leftJoin('todolabels as todolabel','todolabel.id','=','candidatereminder.todolabel_id')
            ->select('candidatereminder.*','admin.name as adminName','careoff.name as careoffName','todolabel.name as todolabelName')
            ->where('candidatereminder.candidate_id','=',$id)
            ->orderBy('candidatereminder.id','DESC')
            ->get();



        $cand_mob_no = $post->mobile_no_dial_code != '' ? $post->mobile_no_dial_code.''.$post->mobile_no : $post->mobile_no;
        $cand_cont_no = $post->contact_no_dial_code != '' ? $post->contact_no_dial_code.''.$post->contact_no : $post->contact_no;
        $cand_rel_cont_no = $post->relative_contact_no_dial_code != '' ? $post->relative_contact_no_dial_code.''.$post->relative_contact_no : $post->relative_contact_no;

        $assocconfirmbys = Canassocconfirmby::where('cand_id','=',$id)->orderBy('id','DESC')->get();

        $post_data = [
            'mobile_no' => $cand_mob_no,
            'contact_no' => $cand_cont_no,
            'relative_contact' => $cand_rel_cont_no
        ];

        $empData = $this->getEmployerByCandidate($id);

        if ($empData) {
            $employerId = $empData['id'];
            $employerType = $empData['type'];  // employerplus | employer
        }


        return view('admin.client.partner.candidate_view',compact('empData','careoffName','assocconfirmbys','post_data','reminders','todolabels','permission','vehtrans','myVehtr','post','expworkcities','expworklocs','candsc','careoffs','associates','partners','paycands','occupation','educationname','educations','religions','religionname','candDownloads','candcount','candBooks','candcity','filePhotos','myCarkn','cars','timelines','placeofbirth','nation','candRegion','poiss','myexpwl','cities','countries','regions','jobtypes','poi','admin','mycont','proffs','myproff','mycity','users','CalAmount','PaytotalAmount','activeService','embassies'));
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required|in:0,1'
        ]);

        User::where('id', $request->id)
            ->update(['status' => $request->status]);

            return response()->json([
                'success' => true
            ]);    
    }

    public function updateFilterList(Request $request){
        $post = Clientfilterlist::where('admin_id','=',Auth::guard('partner')->user()->id)->count();

        if ($post > 0) {
            $updateF = Clientfilterlist::where('admin_id','=',Auth::guard('partner')->user()->id)->first();

            if($request->countryf == 1){
				$updateF->country_filter = true;
			}else{
				$updateF->country_filter = false;
			}

            if($request->cityf == 1){
				$updateF->city_filter = true;
			}else{
				$updateF->city_filter = false;
			}

            if($request->statusf == 1){
				$updateF->status_filter = true;
			}else{
				$updateF->status_filter = false;
			}

            if($request->created_datef == 1){
				$updateF->created_date_filter = true;
			}else{
				$updateF->created_date_filter = false;
			}

            $updateF->save();
    		return response()->json('success');

        } else {
            $newFilter = new Clientfilterlist();

            $newFilter->admin_id = Auth::guard('partner')->user()->id;

            if($request->countryf == 1){
				$newFilter->country_filter = true;
			}else{
				$newFilter->country_filter = false;
			}

            if($request->cityf == 1){
				$newFilter->city_filter = true;
			}else{
				$newFilter->city_filter = false;
			}

            if($request->statusf == 1){
				$newFilter->status_filter = true;
			}else{
				$newFilter->status_filter = false;
			}

            if($request->created_datef == 1){
				$newFilter->created_date_filter = true;
			}else{
				$newFilter->created_date_filter = false;
			}

            $newFilter->save();

            return response()->json('success');
        }
    }

    public function getEmployerByCandidate($cand_id)
    {
        // Find assigned record
        $assign = Employercandidate::where('cand_id', $cand_id)
            ->where('status', 1)
            ->first();
    
        if (!$assign) {
            return null;
        }
    
        // For employerplus (emp_id)
        if (!empty($assign->emp_id)) {
            return [
                'type' => 'employerplus',
                'id'   => $assign->emp_id
            ];
        }
    
        // For employer (emp2_id)
        if (!empty($assign->emp2_id)) {
            return [
                'type' => 'employer',
                'id'   => $assign->emp2_id
            ];
        }
    
        return null;
    }
   
}

