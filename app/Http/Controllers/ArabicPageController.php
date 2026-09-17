<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Candidate;
use App\Models\City;
use App\Models\Country;
use App\Models\Cvsetting;
use App\Models\Expecworkcity;
use App\Models\Placeofissue;
use App\Models\Profession;
use App\Models\Region;
use App\Models\Religion;
use App\Models\Websiteconfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Carnknown;
use App\Models\Education;
use App\Models\Otpcountrycode;
use App\Models\Partner;
use Illuminate\Support\Facades\Session;
use App\Models\Customercost;
use App\Models\Frontendwebsiteconfig;
use Illuminate\Support\Facades\Auth;


class ArabicPageController extends Controller
{
    public function index(){
        $countries = Otpcountrycode::orderBy('id','DESC')->get();
        $websiteLogo = Frontendwebsiteconfig::first();
        $posts = DB::table('candidates as cand')
            ->leftJoin('professions as proff','proff.id','cand.jobtype_id')
            ->select('cand.*','proff.eng_name as pengname','proff.ar_name as arname')
            // ->select('cand.*','proff.ar_name','proff.eng_name')
            ->where('cand.status','=',1)
            ->where('cand.publish','=',1)
            ->where('cand.isdelete','=',0)
            ->where('cand.cv_execute','=',1)
            // ->where('cand.cv_execute_file','!=','')
            // ->orderBy('cand.id','DESC')
            ->get();
        // $posts = Candidate::orderBy('id','DESC')->where('status','=',1)->where('publish','=',1)->where('isdelete','=',0)->get();
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        return view('arabic.user.welcome',compact('posts','countries','frontwebsite'));
    }

    public function about(){
        $countries = Country::orderBy('id','DESC')->get();
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        return view('arabic.user.about',compact('countries','frontwebsite'));
    }

    public function services(){
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        return view('arabic.user.services',compact('frontwebsite'));
    }

    public function contact(){
        $countries = Country::orderBy('id','DESC')->get();
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        return view('arabic.user.contact',compact('countries','frontwebsite'));
    }

    public function resumes(Request $request){
        // $posts = Candidate::where('status','=',1)->where('publish','=',1)->paginate(9);
        
        $cand = DB::table('candidates as cand')
        ->leftJoin('professions as proff','proff.id','=','cand.jobtype_id')
        ->select('cand.*','proff.eng_name as pengname','proff.ar_name as arname')
        ->where('cand.status','=',1)
        ->where('cand.publish','=',1)
        ->where('cand.isdelete','=',0)
        ->where('cand.cv_execute','=',1);
        // ->where('cand.cv_execute_file','!=','');

        if (isset($request->location_id) && $request->location_id != '') {
            $posts = $cand->whereRaw('FIND_IN_SET("'.$request->location_id.'",cand.expwp_id)');
        }

        if (isset($request->proff_id) && $request->proff_id != '') {
            $posts = $cand->whereRaw('FIND_IN_SET("'.$request->proff_id.'",cand.proff_id)');
        }

        if (isset($request->expcity_id) && $request->expcity_id != '') {
            $posts = $cand->where('gulfexperience','=',$request->expcity_id);
        }

        if (isset($request->age)) {
            $age = explode('-',$request->age);
            $minage = $age[0];
            $maxage = $age[1];
            $posts = $cand->whereBetween('cand.age',[$minage,$maxage]);
        }

        // if ((isset($request->minage)) || (isset($request->maxage))) {
        //     $posts = $cand->whereBetween('cand.age',[$request->minage,$request->maxage]);
        // }

        if (isset($request->religions) && $request->religions != '') {
            $posts = $cand->where('religion_id','=',$request->religions);
        }

        $myExp = [];

        // $fresher = array(0);
        // $year12 = array(1,2);
        // $year25 = array(2,3,4,5);
        // $year510 = array(5,6,7,8,9,10);
        // $final_array = array_merge($fresher,$year12,$year25,$year510);
        // $unique_array = array_unique($final_array);
        // $expex = array_push($myExp,"0","1");
        // dd($unique_array);


        if (isset($request->finalExp) && $request->finalExp != '') {
            $expFinal = array_unique($request->finalExp);
            $posts = $cand->whereIn('overall_exp',$expFinal);            
        }

        $posts = $cand->orderBy('cand.id','ASC')->paginate(9)->withQueryString();

        // $cities = City::orderBy('name')->get();
        // $cities = Expecworkcity::orderBy('name')->get();
        $cities = DB::table('expecworkcities')
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('candidates')
                ->whereRaw('FIND_IN_SET(expecworkcities.id, candidates.expwp_id)')
                ->where('status','=',1)
                ->where('publish','=',1)
                ->where('isdelete','=',0)
                ->where('cv_execute','=',1);
        })
        ->orderBy('name')
        ->get();
        
        $jobTypes = Profession::orderBy('eng_name')->get();
        $religion = Religion::orderBy('name')->get();

        $frontwebsite = Frontendwebsiteconfig::first();
       
        if ($request->ajax()) {
            return view('arabic.user.resume.filter',['posts' => $posts,'religions' => $religion,'cities' => $cities,'jobTypes' => $jobTypes])->render();
        }
        $countries = Otpcountrycode::orderBy('id','DESC')->get();
        return view('arabic.user.resumes',['posts' => $posts,'religions' => $religion,'cities' => $cities,'jobTypes' => $jobTypes,'countries' => $countries,'frontwebsite' => $frontwebsite]);
    }
    

    public function fullresumes($id){
        
        if (Auth::check()) {
            Auth::user()->refresh();
        }

        $post = Candidate::where('slug_text','=',$id)->first();
        
        if(isset($post)){
            if($post->video_link != NULL){
                $video_id = explode("/",$post->video_link);
            }else{
                $video_id = "----";
            }
            if($post->trade_test_video_link != NULL){
                $test_video_id = explode("/",$post->trade_test_video_link);
            }else{
                $test_video_id = "----";
            }

            $wps = City::wherein('id',explode(",",$post->expcity_id))->get();
            $expconts = Country::wherein('id',explode(',',$post->expcountry_id))->get();
            // $rel_posts = Candidate::where('status','=',1)->where('publish','=',1)->where('isdelete','=',0)->where('job_type','=',$post->job_type)->where('id','!=',$id)->orderBy('id','DESC')->get();
            $poi = Placeofissue::find($post->poi);
            $total_exp = array_sum(explode(',',$post->experience));
            $webconfig = Websiteconfig::first();
            $booking = Booking::count();
            $education = Education::find($post->education_id);
            $vehicles = Carnknown::wherein('id',explode(',',$post->carknown_id))->get();
            $vehtrans = DB::table('vehical_transmission')->where('id',explode(',',$post->vehical_transmission))->get();

            $rel_posts = DB::table('candidates as cand')
                ->leftJoin('professions as proff','proff.id','=','cand.jobtype_id')
                ->select('cand.*','proff.eng_name as pengname','proff.ar_name as arname')
                ->where('cand.jobtype_id','=',$post->jobtype_id)
                ->where('cand.status','=',1)
                ->where('cand.publish','=',1)
                ->where('cand.isdelete','=',0)
                ->where('cand.cv_execute','=',1)
                ->where('cand.cv_execute_file','!=','')
                // ->where('cand.id','!=',$post->id)
                ->where('cand.slug_text','!=',$id)
                ->orderBy('cand.id',"ASC")
                ->get();

            $nation = Country::find($post->nation_id);
            $region = Region::find($post->region_id);
            $plob = City::find($post->plb_id);
            $religionN = Religion::find($post->religion_id);

            $job_type = Profession::find($post->jobtype_id);

            $max_limit = Websiteconfig::first();

            $expwpf = Expecworkcity::wherein('id',explode(",",$post->expwp_id))->get();
            $notinexpwpf = Expecworkcity::whereNotin('id',explode(",",$post->expwp_id))->get();

            $overtext = Cvsetting::where('db_field_name','=','text_overview')->first();

            $expworkcities = Expecworkcity::orderBy('name')->get();


            $partners = DB::table('partners as partner')
                ->leftJoin('countries as country','partner.country_id','=','country.id')
                ->leftJoin('cities as city','partner.city_id','=','city.id')
                ->where('partner.portal_status', '=', '1')
                ->orderBy('partner.portal_rec_off_arname')
                ->get();


            $mycont = [];
            $mywkp = [];

            $myexpwp = [];
            $mycarknwon = [];

            $myvehtrans = [];

            if (isset($expwpf)) {
                foreach ($expwpf as $expwp) {
                    $myexpwp[] = $expwp->arname;
                }
            }else{
                $myexpwp[] = "في أى مكان";
            }

            foreach ($expconts as $expcont) {
                $mycont[] =  $expcont->arname;
            }
            foreach ($wps as $wp) {
                $mywkp [] = $wp->name;
            }

            if(isset($vehicles)){
                foreach($vehicles as $vehic){
                    $mycarknwon[] = $vehic->name;
                }
            }else{
                $mycarknwon [] = "---";
            }

            foreach($vehtrans as $vehtran){
                $myvehtrans[] = $vehtran->ar_name;
            }

            // $checkCand = Booking::where('cand_id','=',$post->id)->where('partner_id','!=', NULL)->first();
            $checkCand = DB::table('bookings as booking')
                ->leftJoin('partners as partner','booking.partner_id','=','partner.id')
                ->where('booking.cand_id','=',$post->id)
                ->where('booking.partner_id','!=','')
                ->where('partner.portal_status','=','1')
                ->where('booking.booking_status','!=',2)
                ->select('booking.*','partner.portal_ar_add_disp_only')
                ->first();
            // $partner_count = Partner::where('portal_status','=', '1')->first();
            $partner_count = Partner::where('portal_status','=', '1')->get();

            // Service Charges
            // $scCost = Customercost::where('status','=',1)->first();
            $scCost = Customercost::where('proff_id','=',$post->jobtype_id)->where('exp_type','=',$post->gulfexperience)->where('status','=',1)->first();


            if($scCost){
                if(round($scCost->cost,0) == 0){
                    $costCharge = "حر";
                }else{
                    $costCharge = round($scCost->cost,0).' ريال سعودي';
                }
                
                $depDays = $scCost->days.' أيام';
            }else{
                $costCharge = "---";
                $depDays = "---";
            }

            // Booking Requirement
            $bkRequirements = DB::table('requirement_info')->where('language_type','=',2)->get();
            $embassies = DB::table('embassies')->orderBy('embassy')->get();
            $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
            return view('arabic.user.fullresumes',compact('notinexpwpf','costCharge','frontwebsite','embassies','bkRequirements','depDays','overtext','education','mycarknwon','myvehtrans','job_type','expworkcities','post','expwpf','nation','religionN','myexpwp','region','rel_posts','plob','mycont','mywkp','total_exp','poi','partners','webconfig','booking','max_limit','video_id','checkCand','partner_count','test_video_id'));

        }else{
            return view('arabic.page_not_found');
        }


        
        
        
    }
}
