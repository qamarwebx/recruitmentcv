<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Basepathstatus;
use App\Models\Booking;
use App\Models\Candidate;
use App\Models\City;
use App\Models\Country;
use App\Models\Partner;
use App\Models\Placeofissue;
use App\Models\Profession;
use App\Models\Region;
use App\Models\Websiteconfig;
use App\Models\Religion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Carnknown;
use App\Models\Cvsetting;
use App\Models\Downloadcvfromwebsite;
use App\Models\Expecworkcity;
use App\Models\Education;
use App\Models\Userprofile;
use App\Models\Otpcountrycode;
use Illuminate\Support\Facades\Session;
use PDF;
use TCPDF_FONTS;
use App\Models\Customercost;
use App\Models\User;
use App\Models\Whatsappchaturl;

class FrontEndController extends Controller
{
    public function index()
    {
        // $posts = Candidate::orderBy('id','DESC')->where('status','=',1)->where('publish','=',1)->where('isdelete','=',0)->get();
        $posts = DB::table('candidates as cand')
                ->leftJoin('professions as proff','proff.id','=','cand.jobtype_id')
                ->select('cand.*','proff.eng_name as pengname')
                ->where('cand.status','=',1)
                ->where('cand.publish','=',1)
                ->where('cand.isdelete','=',0)
                // ->where('cand.cv_execute_file','!=','')
                ->where('cand.cv_execute','=',1)
                ->get();
        $countries = Otpcountrycode::orderBy('id','asc')->get();
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        return view('welcome', ['countries' => $countries,'posts' => $posts,'frontwebsite' => $frontwebsite]);
    }

    public function about()
    {

        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();

        return view('about',['frontwebsite' => $frontwebsite]);
    }

    public function services()
    {
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        return view('services',['frontwebsite' => $frontwebsite]);
    }

    public function contact()
    {
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        return view('contact',['frontwebsite' => $frontwebsite]);
    }

    // public function resumes(Request $request)
    // {
    //     // $posts = Candidate::where('status','=',1)->where('publish','=',1)->paginate(9);
    //     $cand = DB::table('candidates as cand')
    //     ->leftJoin('professions as proff','proff.id','=','cand.jobtype_id')
    //         ->select('cand.*','proff.eng_name as pengname')
    //         ->where('cand.status','=',1)
    //         ->where('cand.publish','=',1)
    //         ->where('cand.isdelete','=',0)
    //         // ->where('cand.cv_execute_file','!=','')
    //         ->where('cand.cv_execute','=',1);

    //     if (isset($request->location_id) && $request->location_id != '') {
    //         // $posts = $cand->whereRaw('FIND_IN_SET("'.$request->location_id.'",cand.expcity_id)');
    //         $posts = $cand->whereRaw('FIND_IN_SET("'.$request->location_id.'",cand.expwp_id)');
    //     }

    //     if (isset($request->proff_id) && $request->proff_id != '') {
    //         $posts = $cand->whereRaw('FIND_IN_SET("'.$request->proff_id.'",cand.proff_id)');
    //     }

    //     if (isset($request->expcity_id) && $request->expcity_id != '') {
    //         $posts = $cand->where('gulfexperience','=',$request->expcity_id);
    //     }

    //     if(isset($request->age)){
    //         $age = explode("-",$request->age);
    //         $minage = $age[0];
    //         $maxage = $age[1];
    //         $posts = $cand->whereBetween('cand.age',[$minage,$maxage]);
    //     }

    //     // if ((isset($minage)) || (isset($maxage))) {
    //     //     $posts = $cand->whereBetween('cand.age',[$minage,$maxage]);
    //     // }



    //     if (isset($request->religions) && $request->religions != '') {
    //         $posts = $cand->where('religion_id','=',$request->religions);
    //     }

    //     $myExp = [];

    //     // $fresher = array(0);
    //     // $year12 = array(1,2);
    //     // $year25 = array(2,3,4,5);
    //     // $year510 = array(5,6,7,8,9,10);
    //     // $final_array = array_merge($fresher,$year12,$year25,$year510);
    //     // $unique_array = array_unique($final_array);
    //     // $expex = array_push($myExp,"0","1");
    //     // dd($unique_array);


    //     if (isset($request->finalExp) && $request->finalExp != '') {
    //         $expFinal = array_unique($request->finalExp);
    //         $posts = $cand->whereIn('overall_exp',$expFinal);
    //     }



    //     $posts = $cand->orderBy('cand.id','ASC')->paginate(9)->withQueryString();


    //     // $cities = City::orderBy('name')->get();
    //     // $cities = DB::table('expecworkcities')->orderBy('name')->get();
    //     $cities = DB::table('expecworkcities')
    //             ->whereExists(function ($query) {
    //                 $query->select(DB::raw(1))
    //                     ->from('candidates')
    //                     ->whereRaw('FIND_IN_SET(expecworkcities.id, candidates.expwp_id)')
    //                     ->where('status','=',1)
    //                     ->where('publish','=',1)
    //                     ->where('isdelete','=',0)
    //                     ->where('cv_execute','=',1);
    //             })
    //             ->orderBy('name')
    //             ->get();


    //     $jobTypes = Profession::orderBy('eng_name')->get();
    //     $religion = Religion::orderBy('name')->get();
    //     $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
    //     if ($request->ajax()) {
    //         return view('resume.filter',['posts' => $posts,'religions' => $religion,'cities' => $cities,'jobTypes' => $jobTypes])->render();
    //     }
    //     $countries = Otpcountrycode::orderBy('id','asc')->get();
    //     return view('resumes',['posts' => $posts,'religions' => $religion,'cities' => $cities,'jobTypes' => $jobTypes,'countries' => $countries,'frontwebsite' => $frontwebsite]);
    // }
    
    public function resumes(Request $request)
    {
        $cand = Candidate::with([
            'profession:id,eng_name'
        ])
        ->where('status', 1)
        ->where('publish', 1)
        ->where('isdelete', 0)
        ->where('cv_execute', 1);
    
        // 🔹 Location filter (expwp_id)
        if (!empty($request->location_id)) {
            $cand->whereRaw('FIND_IN_SET(?, expwp_id)', [$request->location_id]);
        }
    
        // 🔹 Profession filter (proff_id)
        if (!empty($request->proff_id)) {
            $cand->whereRaw('FIND_IN_SET(?, proff_id)', [$request->proff_id]);
        }
    
        // 🔹 Gulf experience
        if (!empty($request->expcity_id)) {
            $cand->where('gulfexperience', $request->expcity_id);
        }
    
        // 🔹 Age filter
        if (!empty($request->age)) {
            [$minage, $maxage] = explode('-', $request->age);
            $cand->whereBetween('age', [$minage, $maxage]);
        }
    
        // 🔹 Religion
        if (!empty($request->religions)) {
            $cand->where('religion_id', $request->religions);
        }
    
        // 🔹 Experience (overall_exp)
        if (!empty($request->finalExp)) {
            $expFinal = array_unique($request->finalExp);
            $cand->whereIn('overall_exp', $expFinal);
        }
    
        // 🔹 Final result
        $posts = $cand
            ->orderBy('id', 'ASC')
            ->paginate(9)
            ->withQueryString();
    
        // 🔹 Cities (same logic, unchanged)
        $cities = DB::table('expecworkcities')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('candidates')
                    ->whereRaw('FIND_IN_SET(expecworkcities.id, candidates.expwp_id)')
                    ->where('status', 1)
                    ->where('publish', 1)
                    ->where('isdelete', 0)
                    ->where('cv_execute', 1);
            })
            ->orderBy('name')
            ->get();
    
        $jobTypes = Profession::orderBy('eng_name')->get();
        $religion = Religion::orderBy('name')->get();
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
            
        if ($request->ajax()) {
            return view('resume.filter', [
                'posts'     => $posts,
                'religions' => $religion,
                'cities'    => $cities,
                'jobTypes'  => $jobTypes
            ])->render();
        }
    
        $countries = Otpcountrycode::orderBy('id', 'asc')->get();
    
        return view('resumes', [
            'posts'        => $posts,
            'religions'    => $religion,
            'cities'       => $cities,
            'jobTypes'     => $jobTypes,
            'countries'    => $countries,
            'frontwebsite' => $frontwebsite
        ]);
    }


    public function fullresumes($id)
    {
       
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

            if($post->trade_test_video_link != NULL)
            {
                $test_video_id = explode("/",$post->trade_test_video_link);
            }else{
                $test_video_id = "----";
            }

            $wps = City::wherein('id',explode(",",$post->expcity_id))->get();
            $expconts = Country::wherein('id',explode(',',$post->expcountry_id))->get();

            $rel_posts = DB::table('candidates as cand')
                ->leftJoin('professions as proff','proff.id','=','cand.jobtype_id')
                ->select('cand.*','proff.eng_name as pengname')
                ->where('cand.status','=',1)
                ->where('cand.publish','=',1)
                ->where('cand.isdelete','=',0)
                ->where('cand.cv_execute','=',1)
                ->where('cand.cv_execute_file','!=','')
                ->where('cand.job_type','=',$post->job_type)
                ->where('cand.slug_text','!=',$id)
                ->orderBy('cand.id','ASC')->get();
            $poi = Placeofissue::find($post->poi);
            $total_exp = array_sum(explode(',',$post->experience));
            $webconfig = Websiteconfig::first();
            $booking = Booking::count();
            $vehicles = Carnknown::wherein('id',explode(',',$post->carknown_id))->get();
            $proffesion2 = Profession::find($post->jobtype_id);
            $education = Education::find($post->education_id);
            $vehtrans = DB::table('vehical_transmission')->where('id',explode(',',$post->vehical_transmission))->get();

            $nation = Country::find($post->nation_id);
            //dd( $nation );
            $region = Region::find($post->region_id);
            $plob = City::find($post->plb_id);
            $religionN = Religion::find($post->religion_id);

            $max_limit = Websiteconfig::first();

            $expwpf = Expecworkcity::wherein('id',explode(",",$post->expwp_id))->get();

            $notinexpwpf = Expecworkcity::whereNotin('id',explode(",",$post->expwp_id))->get();

            $overtext = Cvsetting::where('db_field_name','=','text_overview')->first();

            $expworkcities = Expecworkcity::orderBy('name')->get();

            $partners = DB::table('partners as partner')
            ->leftJoin('countries as country', 'partner.country_id', '=', 'country.id')
            ->leftJoin('cities as city', 'partner.city_id', '=', 'city.id')
            ->where('partner.portal_status', '=', '1')
            ->orderBy('partner.portal_rec_off_name')
            ->get();


            $mycont = [];
            $mywkp = [];

            $myexpwp = [];

            $mycarknwon = [];

            $myvehtrans = [];

            if (isset($expwpf)) {
                foreach ($expwpf as $expwp) {
                    $myexpwp[] = $expwp->name;
                }
            }else{
                $myexpwp[] = "Anywhere";
            }

            foreach ($expconts as $expcont) {
                $mycont[] =  $expcont->name;
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
                $myvehtrans[] = $vehtran->eng_name;
            }


            // $checkCand = Booking::where('cand_id','=',$post->id)->where('partner_id','!=', NULL)->first();
            $checkCand = DB::table('bookings as booking')
                ->leftJoin('partners as partner','booking.partner_id','=','partner.id')
                ->where('booking.partner_id','!=','')
                ->where('partner.portal_status','=','1')
                ->where('booking.booking_status','!=',2)
                ->where('booking.cand_id','=',$post->id)
                ->select('booking.*','partner.portal_add_disp_only')
                ->first();
            // $partner_count = Partner::where('portal_status','=', '1')->first();
            $partner_count = Partner::where('portal_status','=', '1')->get();

            // Service Charges
            // $scCost = Customercost::where('status','=',1)->first();
            $scCost = Customercost::where('proff_id','=',$post->jobtype_id)->where('exp_type','=',$post->gulfexperience)->where('status','=',1)->first();

            if($scCost){
                if(round($scCost->cost,0) == 0){
                    $costCharge = "Free";
                }else{
                    $costCharge = round($scCost->cost,0).' SAR';
                }
                $depDays = $scCost->days.' Days';
            }else{
                $costCharge = "---";
                $depDays = '---';
            }

            //dd($partner_count);
            // Booking Requirement
            $bkRequirements = DB::table('requirement_info')->where('language_type','=',1)->get();

            $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
            return view('fullresumes',compact('notinexpwpf','bkRequirements','costCharge','frontwebsite','depDays','overtext','education','mycarknwon','myvehtrans','proffesion2','expworkcities','post','expwpf','nation','religionN','myexpwp','region','rel_posts','plob','mycont','mywkp','total_exp','poi','partners','webconfig','booking','max_limit','video_id','checkCand','test_video_id','partner_count'));
        }else{
            return view('page_not_found');
        }
    }

    // Check Auth
    public function checkAuth(Request $request){

        $postID = $request->post_id;

        if(Auth::check()){
            $userStatus = "Login";
        }else{
            $userStatus = "NotLogin";
        }

        $data = [
            'userStatus' => $userStatus,
            'postID' => $postID
        ];

        return response()->json($data);

    }

    public function getexpectedwork(Request $request) {
        $checkpost = Candidate::find($request->cand_id);

        if ($checkpost->expwp_id != '') {
            $post = Candidate::where('id','=',$request->cand_id)->whereRaw("FIND_IN_SET('".$request->city_id."',expwp_id)")->count();

            if($post == 0){
                echo "false";
            }else{
                echo "true";
            }

        }else{
            echo "true";
        }

    }

    public function getexpectedwork2(Request $request){
        $checkpost = Candidate::find($request->cand_id);

        if ($checkpost->expwp_id != '') {
            $post = Candidate::where('id','=',$request->cand_id)->whereRaw("FIND_IN_SET('".$request->city_id."',expwp_id)")->count();

            if($post == 0){
                echo "false";
            }else{
                echo "true";
            }

        }else{
            echo "true";
        }
    }
    public function getexpectedwork3(Request $request){
        $checkpost = Candidate::find($request->cand_id);

        if ($checkpost->expwp_id != '') {
            $post = Candidate::where('id','=',$request->cand_id)->whereRaw("FIND_IN_SET('".$request->city_id."',expwp_id)")->count();

            if($post == 0){
                echo "false";
            }else{
                echo "true";
            }

        }else{
            echo "true";
        }
    }
    public function getexpectedwork4(Request $request){
        $checkpost = Candidate::find($request->cand_id);

        if ($checkpost->expwp_id != '') {
            $post = Candidate::where('id','=',$request->cand_id)->whereRaw("FIND_IN_SET('".$request->city_id."',expwp_id)")->count();

            if($post == 0){
                echo "false";
            }else{
                echo "true";
            }

        }else{
            echo "true";
        }
    }

    public function checkUserMailExists(Request $request){
        $checkEmail = User::where('email','=',$request->email)->count();

        if($checkEmail == 0){
            echo "true";
        }else{
            echo "false";
        }

    }

    public function checkUserMailExists2(Request $request){
        $checkEmail = User::where('email','=',$request->email)->where('email','!=','')->count();

        if($checkEmail == 0){
            return response()->json(['EmailStatus' => '1']);
        }else{
            return response()->json(['EmailStatus' => '0']);
        }

    }

    public function getvisaembassyfor(Request $request){
        $checkpost = Candidate::find($request->cand_id);
        $post = Candidate::where('id','=',$request->cand_id)->where('embassy_for','=',$request->embassy_for)->count();
        if($checkpost->embassy_for != ''){
            if($post == 0){
                echo "false";
            }else{
                echo "true";
            }
        }else{
            echo "true";
        }
    }

    public function basepathstatus(){
        $post = Basepathstatus::first();

        return view('basepath',compact('post'));
    }

    public function basepathstatusupdt(Request $request){
        if($request->baspath_id != ''){
            $post = Basepathstatus::find($request->baspath_id);
            if ($request->base_path_status != '') {
                $post->base_path_status = $request->base_path_status;
            }
            $post->save();
        }else{
            $post = new Basepathstatus();
            if ($request->base_path_status != '') {
                $post->base_path_status = $request->base_path_status;
            }
            $post->save();
        }

        return redirect()->back();
    }

    public function downloadCV($id){

        // Update record in download Cv
        $upPost = new Downloadcvfromwebsite();
        $upPost->cand_id = $id;
        $upPost->user_id = Auth::user()->id;
        $upPost->save();

        $post = DB::table('candidates as cand')
        ->leftJoin('placeofissues as poi','cand.poi','=','poi.id')
        ->leftJoin('countries as nation','cand.nation_id','=','nation.id')
        ->leftJoin('regions as region','cand.region_id','region.id')
        ->leftJoin('cities as candcity','cand.candcity_id','=','candcity.id')
        ->leftJoin('cities as plb','cand.plb_id','plb.id')
        ->leftJoin('professions as occupation','cand.jobtype_id','=','occupation.id')
        ->select('cand.*','poi.name as poiname','nation.name as nationname','region.name as regionname','candcity.name as cancityname','plb.name as plbname','occupation.ar_name as prarname','occupation.eng_name as pengname')
        ->where('cand.id','=',$id)
        ->first();

        // Car Know List
        $carkns = Carnknown::wherein('id',explode(",",$post->carknown_id))->get();
        $carlist = [];
        if(isset($carkns)){
            foreach($carkns as $carkn){
                $carlist [] = $carkn->name;
            }
        }else{
            $carlist [] = "";
        }
        // Expected Work place
        $expwps = City::wherein('id',explode(",",$post->expwp_id))->get();
        $exp_wp = [];
        if(isset($expwps)){
            foreach($expwps as $expwp){
                $exp_wp [] = $expwp->name;
            }
        }else{
            $exp_wp [] = "";
        }



        // Fetch details as per cv setting
        $cand_name_cvsetting = Cvsetting::where('db_field_name','=','cand_name')->first();
        $exp_sal_cvsetting = Cvsetting::where('db_field_name','=','exp_sal')->first();
        $expwp_id_cvsetting = Cvsetting::where('db_field_name','=','expwp_id')->first();
        $age_cvsetting = Cvsetting::where('db_field_name','=','age')->first();
        $marital_status_cvsetting = Cvsetting::where('db_field_name','=','marital_status')->first();
        $religion_cvsetting = Cvsetting::where('db_field_name','=','religion')->first();
        $dob_cvsetting = Cvsetting::where('db_field_name','=','dob')->first();
        $plb_id_cvsetting = Cvsetting::where('db_field_name','=','plb_id')->first();
        $nation_id_cvsetting = Cvsetting::where('db_field_name','=','nation_id')->first();
        $region_id_cvsetting = Cvsetting::where('db_field_name','=','region_id')->first();
        $lang_known_cvsetting = Cvsetting::where('db_field_name','=','lang_known')->first();
        $google_map_cvsetting = Cvsetting::where('db_field_name','=','google_map')->first();
        $carknown_id_cvsetting = Cvsetting::where('db_field_name','=','carknown_id')->first();
        $proff_id_cvsetting = Cvsetting::where('db_field_name','=','proff_id')->first();
        $experience_cvsetting = Cvsetting::where('db_field_name','=','experience')->first();
        $expcountry_id_cvsetting = Cvsetting::where('db_field_name','=','expcountry_id')->first();
        $expcity_id_cvsetting = Cvsetting::where('db_field_name','=','expcity_id')->first();
        $pass_no_cvsetting = Cvsetting::where('db_field_name','=','pass_no')->first();
        $pass_type_cvsetting = Cvsetting::where('db_field_name','=','pass_type')->first();
        $doi_cvsetting = Cvsetting::where('db_field_name','=','doi')->first();
        $doe_cvsetting = Cvsetting::where('db_field_name','=','doe')->first();
        $poi_cvsetting = Cvsetting::where('db_field_name','=','poi')->first();
        $photo_cvsetting = Cvsetting::where('db_field_name','=','photo')->first();
        $fullsize_cvsetting = Cvsetting::where('db_field_name','=','fullsize')->first();
        $reference_no_cvsetting = Cvsetting::where('db_field_name','=','reference_no')->first();
        $gulf_experience_cvsetting = Cvsetting::where('db_field_name','=','gulf_experience')->first();
        $image1_cvsetting = Cvsetting::where('db_field_name','=','image1')->first();
        $image2_cvsetting = Cvsetting::where('db_field_name','=','image2')->first();

        // Base Path Status
        $basepathSt = Basepathstatus::first();

        try {
            // $backfile = base_path().'/public/admin/assets/images/resume/SVG_ONE_FINE_L.svg';
            // $backfile = base_path().'/public/admin/assets/images/resume/final_cv.pdf';
            // $backfile = base_path().'/public/admin/assets/images/resume/resumes_format_svg.svg';
            // $backfile = base_path().'/public/admin/assets/images/resume/SVG3.svg';

            if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                $backfile = base_path('public/admin/assets/images/resume/SVG3.svg');
            }else{
                $backfile = base_path('public/admin/assets/images/resume/SVG3.svg');
            }





            PDF::SetCreator('Qamr International');
            PDF::SetAuthor('Qamr International');
            PDF::SetTitle($post->cand_name.' CV');
            PDF::SetSubject($post->cand_name.' CV');
            PDF::SetKeywords('Qamr, PDF, visa, form, guide');


            PDF::AddPage();
            PDF::ImageSVG($backfile,'','',210,297,'','','',0,false);

            // Candidate Name
            if (isset($cand_name_cvsetting)) {
                if($cand_name_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $candpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $candpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($cand_name_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $candpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $candpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }


                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $candpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $candpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }


                }
                $candpopinsfont = TCPDF_FONTS::addTTFfont($candpopins,'','',32);

                if($cand_name_cvsetting->font_family == 'none'){
                    $candfonttype = "";
                }else{
                    $candfonttype = $cand_name_cvsetting->font_family;
                }

                if($cand_name_cvsetting->font_size != ''){
                    $candfontsize = $cand_name_cvsetting->font_size;
                }else{
                    $candfontsize = '11';
                }

                PDF::SetFont($candpopinsfont,$candfonttype, $candfontsize,'',false);
                if($cand_name_cvsetting->font_color != ''){
                    $candfontcolor = explode(",",$cand_name_cvsetting->font_color);
                    PDF::SetTextColor($candfontcolor[0],$candfontcolor[1],$candfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }

                PDF::SetXY($cand_name_cvsetting->x_axis,$cand_name_cvsetting->y_axis);
                PDF::Cell(0,0,$post->cand_name);

            }

            // Image1 and Image2
            if(isset($image1_cvsetting)){
                if($image1_cvsetting->status == 1 && $image1_cvsetting->filename != ''){

                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $image1_cvsettingpath = base_path().'/public/admin/assets/images/cv_setting/'.$image1_cvsetting->filename;
                    }else{
                        $image1_cvsettingpath = base_path('public/admin/assets/images/cv_setting/'.$image1_cvsetting->filename);
                    }
                    $image1_cvsettingext = pathinfo($image1_cvsetting->filename, PATHINFO_EXTENSION);

                    PDF::Image($image1_cvsettingpath,$image1_cvsetting->x_axis, $image1_cvsetting->y_axis, $image1_cvsetting->width, '',$image1_cvsettingext,'','',false,'300','',false,false,0,false,false,false);
                }
            }

            if(isset($image2_cvsetting)){
                if($image2_cvsetting->status == 1 && $image2_cvsetting->filename != ''){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $image2_cvsettingpath = base_path().'/public/admin/assets/images/cv_setting/'.$image2_cvsetting->filename;
                    }else{
                        $image2_cvsettingpath = base_path('public/admin/assets/images/cv_setting/'.$image2_cvsetting->filename);
                    }

                    $image2_cvsettingext = pathinfo($image2_cvsetting->filename, PATHINFO_EXTENSION);

                    PDF::Image($image2_cvsettingpath,$image2_cvsetting->x_axis, $image2_cvsetting->y_axis, $image2_cvsetting->width, '',$image2_cvsettingext,'','',false,'300','',false,false,0,false,false,false);
                }
            }

            // Profile and fullsize Photo
            if ($post->photo_file != '') {
                if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                    $photfilepath = base_path().'/public/admin/assets/images/candidate/'.$post->photo_file;
                }else{
                    $photfilepath = base_path('public/admin/assets/images/candidate/'.$post->photo_file);
                }

                $photext = pathinfo($post->photo_file, PATHINFO_EXTENSION);

                if(isset($photo_cvsetting)){
                    PDF::Image($photfilepath,$photo_cvsetting->x_axis,$photo_cvsetting->y_axis,'50','54',$photext,'','',false,'300','',false,false,0,false,false,false);
                }
            }
            if ($post->cv_file != '') {
                if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                    $fullsizepath = base_path().'/public/admin/assets/images/candidate/'.$post->cv_file;
                }else{
                    $fullsizepath = base_path('public/admin/assets/images/candidate/'.$post->cv_file);
                }
                $fullsizeext = pathinfo($post->cv_file, PATHINFO_EXTENSION);

                if(isset($fullsize_cvsetting)){
                    PDF::Image($fullsizepath,$fullsize_cvsetting->x_axis, $fullsize_cvsetting->y_axis, '60', '128',$fullsizeext,'','',false,'300','',false,false,0,false,false,false);
                }
            }
            // Reference No
            if (isset($reference_no_cvsetting)) {
                if($reference_no_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $refrenceNopopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                       $refrenceNopopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($reference_no_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $refrenceNopopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $refrenceNopopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $refrenceNopopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $refrenceNopopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }

                }
                $refrenceNopopinsfont = TCPDF_FONTS::addTTFfont($refrenceNopopins,'','',32);

                if($reference_no_cvsetting->font_family == 'none'){
                    $refereneceFont = "";
                }else{
                    $refereneceFont = $reference_no_cvsetting->font_family;
                }

                if($reference_no_cvsetting->font_size != ''){
                    $refrenceNoFontsize = $reference_no_cvsetting->font_size;
                }else{
                    $refrenceNoFontsize = '11';
                }

                PDF::SetFont($refrenceNopopinsfont,$refereneceFont, $refrenceNoFontsize,'',false);
                if($reference_no_cvsetting->font_color != ''){
                    $refnofontcolor = explode(",",$reference_no_cvsetting->font_color);
                    PDF::SetTextColor($refnofontcolor[0],$refnofontcolor[1],$refnofontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }

                PDF::SetXY($reference_no_cvsetting->x_axis,$reference_no_cvsetting->y_axis);
                PDF::Cell(0,0,'Reference No: '.$post->id);

            }
            // Gulf experience and occupation
            if(isset($gulf_experience_cvsetting)){
                if($gulf_experience_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $gulf_experience_arabic = base_path().'/public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Medium.ttf';
                    }else{
                        $gulf_experience_arabic = base_path('public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Medium.ttf');
                    }

                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $gulf_experience_arabic = base_path().'/public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Regular.ttf';
                    }else{
                        $gulf_experience_arabic = base_path('public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Regular.ttf');
                    }
                }
                $gulf_experience_arabic_font = TCPDF_FONTS::addTTFfont($gulf_experience_arabic,'','',32);

                if($gulf_experience_cvsetting->font_family == 'none'){
                    $gulfexpFont = "";
                }else{
                    $gulfexpFont = $gulf_experience_cvsetting->font_family;
                }

                if($gulf_experience_cvsetting->font_size != ''){

                    $gulfexpFontsize = $gulf_experience_cvsetting->font_size;
                }else{
                    $gulfexpFontsize = "11";
                }
                PDF::SetFont($gulf_experience_arabic_font, $gulfexpFont, $gulfexpFontsize,'',false);

                if($gulf_experience_cvsetting->font_color != ''){
                    $gulfexpfontcolor = explode(",",$gulf_experience_cvsetting->font_color);
                    PDF::SetTextColor($gulfexpfontcolor[0],$gulfexpfontcolor[1],$gulfexpfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }

                PDF::SetXY($gulf_experience_cvsetting->x_axis,$gulf_experience_cvsetting->y_axis);
                if($post->gulfexperience == 1){
                    $gulfexperience1 = $post->prarname.' قد اشتغل في الهند فقط';
                    PDF::Cell(0,0,$gulfexperience1);
                }

                if($post->gulfexperience == 2){
                    $gulfexperience2 = $post->prarname.' سبق له العمل';
                    PDF::Cell(0,0,$gulfexperience2);
                }

                if($post->gulfexperience == 1){
                    PDF::SetFont($gulf_experience_arabic_font, $gulfexpFont, $gulfexpFontsize,'',false);
                    if($gulf_experience_cvsetting->font_color != ''){
                        $gulfexpfontcolor = explode(",",$gulf_experience_cvsetting->font_color);
                        PDF::SetTextColor($gulfexpfontcolor[0],$gulfexpfontcolor[1],$gulfexpfontcolor[2]);
                    }else{
                        PDF::SetTextColor(0,0,0);
                    }
                    $gyaxis = $gulf_experience_cvsetting->y_axis + 4;
                    PDF::SetXY($gulf_experience_cvsetting->x_axis,$gyaxis);
                    PDF::Cell(0,0,'Indian Experience '.$post->pengname);
                }

                if($post->gulfexperience == 2){
                    PDF::SetFont($gulf_experience_arabic_font, $gulfexpFont, $gulfexpFontsize,'',false);
                    if($gulf_experience_cvsetting->font_color != ''){
                        $gulfexpfontcolor = explode(",",$gulf_experience_cvsetting->font_color);
                        PDF::SetTextColor($gulfexpfontcolor[0],$gulfexpfontcolor[1],$gulfexpfontcolor[2]);
                    }else{
                        PDF::SetTextColor(0,0,0);
                    }
                    $gyaxis = $gulf_experience_cvsetting->y_axis + 4;
                    PDF::SetXY($gulf_experience_cvsetting->x_axis,$gyaxis);
                    PDF::Cell(0,0,'Ex-Abroad '.$post->pengname);
                }
            }

            // Expected salary and location
            if(isset($exp_sal_cvsetting)){
                if($exp_sal_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $expsalpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $expsalpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($exp_sal_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $expsalpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $expsalpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $expsalpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $expsalpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $expsalpopinsfont = TCPDF_FONTS::addTTFfont($expsalpopins,'','',32);

                if($exp_sal_cvsetting->font_family == 'none'){
                    $expsalfonttype = "";
                }else{
                    $expsalfonttype = $exp_sal_cvsetting->font_family;
                }

                if($exp_sal_cvsetting->font_size != ''){
                    $expsalfontsize = $exp_sal_cvsetting->font_size;
                }else{
                    $expsalfontsize = "11";
                }

                PDF::SetFont($expsalpopinsfont, $expsalfonttype, $expsalfontsize,'',false);
                if($exp_sal_cvsetting->font_color != ''){
                    $expsalfontcolor = explode(",",$exp_sal_cvsetting->font_color);
                    PDF::SetTextColor($expsalfontcolor[0],$expsalfontcolor[1],$expsalfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($exp_sal_cvsetting->x_axis,$exp_sal_cvsetting->y_axis);
                PDF::Cell(0,0,$post->exp_sal);
            }

            if(isset($expwp_id_cvsetting)){
                if($expwp_id_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $expwppopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $expwppopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }

                }elseif($expwp_id_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $expwppopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $expwppopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $expwppopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $expwppopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $expwppopinsfont = TCPDF_FONTS::addTTFfont($expwppopins,'','',32);

                if($expwp_id_cvsetting->font_family == 'none'){
                    $expwpfonttype = "";
                }else{
                    $expwpfonttype = $expwp_id_cvsetting->font_family;
                }

                if($expwp_id_cvsetting->font_size != ''){
                    $expwpfontsize = $expwp_id_cvsetting->font_size;
                }else{
                    $expwpfontsize = "11";
                }

                PDF::SetFont($expwppopinsfont, $expwpfonttype, $expwpfontsize,'',false);
                if($expwp_id_cvsetting->font_color != ''){
                    $expwpfontcolor = explode(",",$expwp_id_cvsetting->font_color);
                    PDF::SetTextColor($expwpfontcolor[0],$expwpfontcolor[1],$expwpfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($expwp_id_cvsetting->x_axis,$expwp_id_cvsetting->y_axis);
                PDF::Cell(0,0,implode(",",$exp_wp));
            }

            // Age
            if(isset($age_cvsetting)){
                if($age_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $agepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $agepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($age_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $agepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $agepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $agepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $agepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $agepopinsfont = TCPDF_FONTS::addTTFfont($agepopins,'','',32);

                if($age_cvsetting->font_family == 'none'){
                    $agefonttype = "";
                }else{
                    $agefonttype = $age_cvsetting->font_family;
                }

                if($age_cvsetting->font_size != ''){
                    $agefontsize = $age_cvsetting->font_size;
                }else{
                    $agefontsize = "11";
                }

                PDF::SetFont($agepopinsfont, $agefonttype, $agefontsize,'',false);
                if($age_cvsetting->font_color != ''){
                    $agefontcolor = explode(",",$age_cvsetting->font_color);
                    PDF::SetTextColor($agefontcolor[0],$agefontcolor[1],$agefontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($age_cvsetting->x_axis,$age_cvsetting->y_axis);
                PDF::Cell(0,0,$post->age);

            }
            // Marital
            if(isset($marital_status_cvsetting)){
                if($marital_status_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $maritalpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $maritalpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($marital_status_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $maritalpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $maritalpopins = base_path('/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $maritalpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $maritalpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $maritalpopinsfont = TCPDF_FONTS::addTTFfont($maritalpopins,'','',32);


                if($marital_status_cvsetting->font_family == 'none'){
                    $mariedfonttype = "";
                }else{
                    $mariedfonttype = $marital_status_cvsetting->font_family;
                }

                if($marital_status_cvsetting->font_size != ''){
                    $mariedfontsize = $marital_status_cvsetting->font_size;
                }else{
                    $mariedfontsize = "11";
                }

                PDF::SetFont($maritalpopinsfont, $mariedfonttype, $mariedfontsize,'',false);
                if($marital_status_cvsetting->font_color != ''){
                    $marital_statusfontcolor = explode(",",$marital_status_cvsetting->font_color);
                    PDF::SetTextColor($marital_statusfontcolor[0],$marital_statusfontcolor[1],$marital_statusfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($marital_status_cvsetting->x_axis,$marital_status_cvsetting->y_axis);
                PDF::Cell(0,0,$post->marital_status);

            }
            // Religion
            if(isset($religion_cvsetting)){
                if($religion_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $religionpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $religionpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($religion_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $religionpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $religionpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $religionpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $religionpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $religionpopinsfont = TCPDF_FONTS::addTTFfont($religionpopins,'','',32);

                if($religion_cvsetting->font_family == 'none'){
                    $relfonttype = "";
                }else{
                    $relfonttype = $religion_cvsetting->font_family;
                }

                if($religion_cvsetting->font_size != ''){
                    $relfontsize = $religion_cvsetting->font_size;
                }else{
                    $relfontsize = "11";
                }

                PDF::SetFont($religionpopinsfont, $relfonttype, $relfontsize,'',false);
                if($religion_cvsetting->font_color != ''){
                    $religionfontcolor = explode(",",$religion_cvsetting->font_color);
                    PDF::SetTextColor($religionfontcolor[0],$religionfontcolor[1],$religionfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($religion_cvsetting->x_axis,$religion_cvsetting->y_axis);
                PDF::Cell(0,0,$post->religion);

            }
            // Date of Birth
            if(isset($dob_cvsetting)){

                if($dob_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $dobpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $dobpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($dob_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $dobpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $dobpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $dobpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $dobpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }

                }

                $dobpopinsfont = TCPDF_FONTS::addTTFfont($dobpopins,'','',32);

                if($dob_cvsetting->font_family == 'none'){
                    $dobfonttype = "";
                }else{
                    $dobfonttype = $dob_cvsetting->font_family;
                }

                if($dob_cvsetting->font_size != ''){
                    $dobfontsize = $dob_cvsetting->font_size;
                }else{
                    $dobfontsize = "11";
                }

                PDF::SetFont($dobpopinsfont, $dobfonttype, $dobfontsize,'',false);
                if($dob_cvsetting->font_color != ''){
                    $dobfontcolor = explode(",",$dob_cvsetting->font_color);
                    PDF::SetTextColor($dobfontcolor[0],$dobfontcolor[1],$dobfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($dob_cvsetting->x_axis,$dob_cvsetting->y_axis);
                PDF::Cell(0,0,date('d-m-Y',strtotime($post->dob)));

            }
            // Place of Birth
            if(isset($plb_id_cvsetting)){
                if($plb_id_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $plb_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $plb_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($plb_id_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $plb_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $plb_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $plb_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $plb_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }
                $plb_idpopinsfont = TCPDF_FONTS::addTTFfont($plb_idpopins,'','',32);

                if($plb_id_cvsetting->font_family == 'none'){
                    $plb_idfonttype = "";
                }else{
                    $plb_idfonttype = $plb_id_cvsetting->font_family;
                }

                if($plb_id_cvsetting->font_size != ''){
                    $plb_idfontsize = $plb_id_cvsetting->font_size;
                }else{
                    $plb_idfontsize = "11";
                }

                PDF::SetFont($plb_idpopinsfont, $plb_idfonttype, $plb_idfontsize,'',false);
                if($plb_id_cvsetting->font_color != ''){
                    $plb_idfontcolor = explode(",",$plb_id_cvsetting->font_color);
                    PDF::SetTextColor($plb_idfontcolor[0],$plb_idfontcolor[1],$plb_idfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($plb_id_cvsetting->x_axis,$plb_id_cvsetting->y_axis);
                PDF::Cell(0,0,$post->plbname);
            }
            // Nationality
            if(isset($nation_id_cvsetting)){

                if($nation_id_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $nation_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $nation_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($nation_id_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $nation_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $nation_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $nation_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $nation_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }

                }

                $nation_idpopinsfont = TCPDF_FONTS::addTTFfont($nation_idpopins,'','',32);

                if($nation_id_cvsetting->font_family == 'none'){
                    $nation_idfonttype = "";
                }else{
                    $nation_idfonttype = $nation_id_cvsetting->font_family;
                }

                if($nation_id_cvsetting->font_size != ''){

                    $nation_idfontsize = $nation_id_cvsetting->font_size;
                }else{
                    $nation_idfontsize = "11";
                }

                PDF::SetFont($nation_idpopinsfont, $nation_idfonttype, $nation_idfontsize,'',false);
                if($nation_id_cvsetting->font_color != ''){
                    $nation_idfontcolor = explode(",",$nation_id_cvsetting->font_color);
                    PDF::SetTextColor($nation_idfontcolor[0],$nation_idfontcolor[1],$nation_idfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($nation_id_cvsetting->x_axis,$nation_id_cvsetting->y_axis);
                PDF::Cell(0,0,$post->nationname);

            }
            // Region
            if(isset($region_id_cvsetting)){

                if($region_id_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $regionpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $regionpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($region_id_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $regionpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $regionpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $regionpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $regionpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $regionpopinsfont = TCPDF_FONTS::addTTFfont($regionpopins,'','',32);

                if($region_id_cvsetting->font_family == 'none'){
                    $region_idfonttype = "";
                }else{
                    $region_idfonttype = $region_id_cvsetting->font_family;
                }

                if($region_id_cvsetting->font_size != ''){
                    $region_idfonttype = $region_id_cvsetting->font_size;
                }else{
                    $region_idfonttype = "11";
                }

                PDF::SetFont($regionpopinsfont, $region_idfonttype, $region_idfonttype,'',false);
                if($region_id_cvsetting->font_color != ''){
                    $region_idfontcolor = explode(",",$region_id_cvsetting->font_color);
                    PDF::SetTextColor($region_idfontcolor[0],$region_idfontcolor[1],$region_idfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($region_id_cvsetting->x_axis,$region_id_cvsetting->y_axis);
                PDF::Cell(0,0,$post->regionname);
                }

            // Language
            if(isset($lang_known_cvsetting)){

                if($lang_known_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $langknownpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $langknownpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($lang_known_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $langknownpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $langknownpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $langknownpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $langknownpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $langknownpopinsfont = TCPDF_FONTS::addTTFfont($langknownpopins,'','',32);

                if($lang_known_cvsetting->font_family == 'none'){
                    $lang_knownfonttype = "";
                }else{
                    $lang_knownfonttype = $lang_known_cvsetting->font_family;
                }

                if($lang_known_cvsetting->font_size != ''){
                    $lang_knownfontsize = $lang_known_cvsetting->font_size;
                }else{
                    $lang_knownfontsize = "11";
                }

                PDF::SetFont($langknownpopinsfont, $lang_knownfonttype, $lang_knownfontsize,'',false);
                if($lang_known_cvsetting->font_color != ''){
                    $lang_knownfontcolor = explode(",",$lang_known_cvsetting->font_color);
                    PDF::SetTextColor($lang_knownfontcolor[0],$lang_knownfontcolor[1],$lang_knownfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($lang_known_cvsetting->x_axis,$lang_known_cvsetting->y_axis);
                PDF::Cell(0,0,$post->lang_known);

            }
            // Google Map
            if(isset($google_map_cvsetting)){

                if($google_map_cvsetting->font_family == 'none'){
                    $google_mapfonttype = "";
                }else{
                    $google_mapfonttype = $google_map_cvsetting->font_family;
                }

                if($google_map_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $google_map_arabic = base_path().'/public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Medium.ttf';
                    }else{
                        $google_map_arabic = base_path('public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Medium.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $google_map_arabic = base_path().'/public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Regular.ttf';
                    }else{
                        $google_map_arabic = base_path('public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Regular.ttf');
                    }
                }

                $google_map_arabic_font = TCPDF_FONTS::addTTFfont($google_map_arabic,'','',32);

                if($google_map_cvsetting->font_size != ''){
                    $google_mapfontsize = $google_map_cvsetting->font_size;
                }else{
                    $google_mapfontsize = "11";
                }

                if($post->google_map == 1){
                    $gmap = "Yes  نعم";
                }else{
                    $gmap = "No  لا";
                }

                PDF::SetFont($google_map_arabic_font, $google_mapfonttype, $google_mapfontsize,'',false);
                if($google_map_cvsetting->font_color != ''){
                    $google_mapfontcolor = explode(",",$google_map_cvsetting->font_color);
                    PDF::SetTextColor($google_mapfontcolor[0],$google_mapfontcolor[1],$google_mapfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($google_map_cvsetting->x_axis,$google_map_cvsetting->y_axis);
                PDF::Cell(0,0,$gmap);

            }
            // Vehical
            if(isset($carknown_id_cvsetting)){

                if($carknown_id_cvsetting->font_family == 'B'){

                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $carknown_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $carknown_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }


                }elseif($carknown_id_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $carknown_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $carknown_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }

                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $carknown_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $carknown_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $carknown_idpopinsfont = TCPDF_FONTS::addTTFfont($carknown_idpopins,'','',32);

                if($carknown_id_cvsetting->font_family == 'none'){
                    $carknown_idfonttype = "";
                }else{
                    $carknown_idfonttype = $carknown_id_cvsetting->font_family;
                }

                if($carknown_id_cvsetting->font_size != ''){
                    $carknown_idfontsize = $carknown_id_cvsetting->font_size;
                }else{
                    $carknown_idfontsize = "11";
                }

                PDF::SetFont($carknown_idpopinsfont, $carknown_idfonttype, $carknown_idfontsize,'',false);
                if($carknown_id_cvsetting->font_color != ''){
                    $carknown_idfontcolor = explode(",",$carknown_id_cvsetting->font_color);
                    PDF::SetTextColor($carknown_idfontcolor[0],$carknown_idfontcolor[1],$carknown_idfontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($carknown_id_cvsetting->x_axis,$carknown_id_cvsetting->y_axis);
                PDF::Cell(0,0,implode(",",$carlist));
            }

            // Work Experience
            $cexperience = explode(",",$post->experience);
            $cprofessions = explode(",",$post->proff_id);
            $cexpcountry = explode(",",$post->expcountry_id);
            $cexpcity = explode(",",$post->expcity_id);

            $iec = 0;
            $iprof = 0;
            $icont = 0;
            $icity = 0;

            foreach($cexperience as $index => $value){

                // Job
                $professionF = Profession::find($cprofessions[$index]);
                if(isset($proff_id_cvsetting)){
                    $py_axis = $proff_id_cvsetting->y_axis + $iprof;

                    if($proff_id_cvsetting->font_family == 'none'){
                        $proff_idfonttype = "";
                    }else{
                        $proff_idfonttype = $proff_id_cvsetting->font_family;
                    }

                    if($proff_id_cvsetting->font_family == 'B'){
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $proff_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                        }else{
                            $proff_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                        }

                    }elseif($proff_id_cvsetting->font_family == 'I'){
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $proff_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                        }else{
                            $proff_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                        }
                    }else{
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $proff_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                        }else{
                            $proff_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                        }
                    }

                    $proff_idpopinsfont = TCPDF_FONTS::addTTFfont($proff_idpopins,'','',32);

                    if($proff_id_cvsetting->font_size != ''){
                        $proff_idfontsize = $proff_id_cvsetting->font_size;
                    }else{
                        $proff_idfontsize = "11";
                    }

                    PDF::SetFont($proff_idpopinsfont, $proff_idfonttype, $proff_idfontsize,'',false);

                    if($proff_id_cvsetting->font_color != ''){
                        $proff_idfontcolor = explode(",",$proff_id_cvsetting->font_color);
                        PDF::SetTextColor($proff_idfontcolor[0],$proff_idfontcolor[1],$proff_idfontcolor[2]);
                    }else{
                        PDF::SetTextColor(0,0,0);
                    }

                    PDF::SetXY($proff_id_cvsetting->x_axis,$py_axis);
                    PDF::Cell(0,0,$professionF->eng_name);

                    $iprof = $iprof + $proff_id_cvsetting->add_y_axis;
                }
                // Country
                if(isset($expcountry_id_cvsetting)){
                    $cony_axis = $expcountry_id_cvsetting->y_axis + $icont;

                    if($expcountry_id_cvsetting->font_family == 'none'){
                        $expcountry_idfonttype = "";
                    }else{
                        $expcountry_idfonttype = $expcountry_id_cvsetting->font_family;
                    }

                    if($expcountry_id_cvsetting->font_family == 'B'){
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $expcountry_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                        }else{
                            $expcountry_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                        }

                    }elseif($expcountry_id_cvsetting->font_family == 'I'){
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $expcountry_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                        }else{
                            $expcountry_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                        }

                    }else{
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $expcountry_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                        }else{
                            $expcountry_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                        }
                    }

                    $expcountry_idpopinsfont = TCPDF_FONTS::addTTFfont($expcountry_idpopins,'','',32);

                    if($expcountry_id_cvsetting->font_size != ''){
                        $expcountry_idfontsize = $expcountry_id_cvsetting->font_size;
                    }else{
                        $expcountry_idfontsize = "11";
                    }

                    $countryF = Country::find($cexpcountry[$index]);
                    PDF::SetFont($expcountry_idpopinsfont, $expcountry_idfonttype, $expcountry_idfontsize,'',false);
                    if($expcountry_id_cvsetting->font_color != ''){
                        $expcountry_idfontcolor = explode(",",$expcountry_id_cvsetting->font_color);
                        PDF::SetTextColor($expcountry_idfontcolor[0],$expcountry_idfontcolor[1],$expcountry_idfontcolor[2]);
                    }else{
                        PDF::SetTextColor(0,0,0);
                    }
                    PDF::SetXY($expcountry_id_cvsetting->x_axis,$cony_axis);
                    PDF::Cell(0,0,$countryF->name);

                    $icont = $icont + $expcountry_id_cvsetting->add_y_axis;
                }
                // City
                if(isset($expcity_id_cvsetting)){
                    $cit_axis = $expcity_id_cvsetting->y_axis + $icity;

                    if($expcity_id_cvsetting->font_family == 'none'){
                        $expcity_idfonttype = "";
                    }else{
                        $expcity_idfonttype = $expcity_id_cvsetting->font_family;
                    }

                    if($expcity_id_cvsetting->font_family == 'B'){
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $expcity_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                        }else{
                            $expcity_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                        }
                    }elseif($expcity_id_cvsetting->font_family == 'I'){
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $expcity_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                        }else{
                            $expcity_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                        }
                    }else{
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $expcity_idpopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                        }else{
                            $expcity_idpopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                        }
                    }

                    $expcity_idpopinsfont = TCPDF_FONTS::addTTFfont($expcity_idpopins,'','',32);

                    if($expcity_id_cvsetting->font_size != ''){
                        $expcity_idfontsize = $expcity_id_cvsetting->font_size;
                    }else{
                        $expcity_idfontsize = "11";
                    }

                    $cityF = City::find($cexpcity[$index]);
                    PDF::SetFont($expcity_idpopinsfont, $expcity_idfonttype, $expcity_idfontsize,'',false);
                    if($expcity_id_cvsetting->font_color != ''){
                        $expcity_idfontcolor = explode(",",$expcity_id_cvsetting->font_color);
                        PDF::SetTextColor($expcity_idfontcolor[0],$expcity_idfontcolor[1],$expcity_idfontcolor[2]);
                    }else{
                        PDF::SetTextColor(0,0,0);
                    }
                    PDF::SetXY($expcity_id_cvsetting->x_axis,$cit_axis);
                    PDF::Cell(0,0,$cityF->name);

                    $icity = $icity + $expcity_id_cvsetting->add_y_axis;

                }
                // Experience
                if(isset($experience_cvsetting)){
                    $ey_axis = $experience_cvsetting->y_axis + $iec;

                    if($experience_cvsetting->font_family == 'none'){
                        $experiencefonttype = "";
                    }else{
                        $experiencefonttype = $experience_cvsetting->font_family;
                    }

                    if($experience_cvsetting->font_family == 'B'){
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $experiencepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                        }else{
                            $experiencepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                        }
                    }elseif($experience_cvsetting->font_family == 'I'){
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $experiencepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                        }else{
                            $experiencepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                        }

                    }else{
                        if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                            $experiencepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                        }else{
                            $experiencepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                        }
                    }

                    $experiencepopinsfont = TCPDF_FONTS::addTTFfont($experiencepopins,'','',32);

                    if($experience_cvsetting->font_size != ''){
                        $experiencefontsize = $experience_cvsetting->font_size;
                    }else{
                        $experiencefontsize = "11";
                    }

                    PDF::SetFont($experiencepopinsfont, $experiencefonttype, $experiencefontsize,'',false);
                    if($experience_cvsetting->font_color != ''){
                        $experiencefontcolor = explode(",",$experience_cvsetting->font_color);
                        PDF::SetTextColor($experiencefontcolor[0],$experiencefontcolor[1],$experiencefontcolor[2]);
                    }else{
                        PDF::SetTextColor(0,0,0);
                    }
                    PDF::SetXY($experience_cvsetting->x_axis,$ey_axis);
                    PDF::Cell(0,0,$value.' Years');

                    $iec = $iec + $experience_cvsetting->add_y_axis;
                }
            }

            // Passport No
            if(isset($pass_no_cvsetting)){

                if($pass_no_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $pass_nopopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $pass_nopopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($pass_no_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $pass_nopopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $pass_nopopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $pass_nopopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $pass_nopopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $pass_nopopinsfont = TCPDF_FONTS::addTTFfont($pass_nopopins,'','',32);

                if($pass_no_cvsetting->font_family == 'none'){
                    $pass_nofonttype = "";
                }else{
                    $pass_nofonttype = $pass_no_cvsetting->font_family;
                }

                if($pass_no_cvsetting->font_size != ''){
                    $pass_nofontsize = $pass_no_cvsetting->font_size;

                }else{
                    $pass_nofontsize = "11";
                }

                PDF::SetFont($pass_nopopinsfont, $pass_nofonttype, $pass_nofontsize,'',false);
                if($pass_no_cvsetting->font_color != ''){
                    $pass_nofontcolor = explode(",",$pass_no_cvsetting->font_color);
                    PDF::SetTextColor($pass_nofontcolor[0],$pass_nofontcolor[1],$pass_nofontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($pass_no_cvsetting->x_axis,$pass_no_cvsetting->y_axis);
                PDF::Cell(0,0,$post->pass_no);
            }
            // Passport Type
            if(isset($pass_type_cvsetting)){
                if($pass_type_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $pass_typepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $pass_typepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($pass_type_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $pass_typepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $pass_typepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $pass_typepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $pass_typepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $pass_typepopinsfont = TCPDF_FONTS::addTTFfont($pass_typepopins,'','',32);

                if($pass_type_cvsetting->font_family == 'none'){
                    $pass_typefonttype = "";
                }else{
                    $pass_typefonttype = $pass_type_cvsetting->font_family;
                }

                if($pass_type_cvsetting->font_size != ''){
                    $pass_typefontsize = $pass_type_cvsetting->font_size;

                }else{
                    $pass_typefontsize = "11";
                }

                PDF::SetFont($pass_typepopinsfont, $pass_typefonttype, $pass_typefontsize,'',false);
                if($pass_type_cvsetting->font_color != ''){
                    $pass_typefontcolor = explode(",",$pass_type_cvsetting->font_color);
                    PDF::SetTextColor($pass_typefontcolor[0],$pass_typefontcolor[1],$pass_typefontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($pass_type_cvsetting->x_axis,$pass_type_cvsetting->y_axis);
                PDF::Cell(0,0,$post->pass_type);
            }
            // Date of issue
            if(isset($doi_cvsetting)){

                if($doi_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $doipopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $doipopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($doi_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $doipopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $doipopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $doipopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $doipopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }

                }

                $doipopinsfont = TCPDF_FONTS::addTTFfont($doipopins,'','',32);

                if($doi_cvsetting->font_family == 'none'){
                    $doifonttype = "";
                }else{
                    $doifonttype = $doi_cvsetting->font_family;
                }

                if($doi_cvsetting->font_size != ''){
                    $doifontsize = $doi_cvsetting->font_size;

                }else{
                    $doifontsize = "11";
                }

                PDF::SetFont($doipopinsfont, $doifonttype, $doifontsize,'',false);
                if($doi_cvsetting->font_color != ''){
                    $doifontcolor = explode(",",$doi_cvsetting->font_color);
                    PDF::SetTextColor($doifontcolor[0],$doifontcolor[1],$doifontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($doi_cvsetting->x_axis,$doi_cvsetting->y_axis);
                PDF::Cell(0,0,date('d-m-Y',strtotime($post->doi)));
            }
            // Date of expiry
            if(isset($doe_cvsetting)){
                if($doe_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $doepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $doepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }

                }elseif($doe_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $doepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $doepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $doepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $doepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $doepopinsfont = TCPDF_FONTS::addTTFfont($doepopins,'','',32);

                if($doe_cvsetting->font_family == 'none'){
                    $doefonttype = "";
                }else{
                    $doefonttype = $doe_cvsetting->font_family;
                }

                if($doe_cvsetting->font_size != ''){
                    $doefontsize = $doe_cvsetting->font_size;
                }else{
                    $doefontsize = "11";
                }

                PDF::SetFont($doepopinsfont, $doefonttype, $doefontsize,'',false);
                if($doe_cvsetting->font_color != ''){
                    $doefontcolor = explode(",",$doe_cvsetting->font_color);
                    PDF::SetTextColor($doefontcolor[0],$doefontcolor[1],$doefontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($doe_cvsetting->x_axis,$doe_cvsetting->y_axis);
                PDF::Cell(0,0,date('d-m-Y',strtotime($post->doe)));
            }
            // Place of Issue
            if(isset($poi_cvsetting)){
                if($poi_cvsetting->font_family == 'B'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $poipopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf';
                    }else{
                        $poipopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Medium.ttf');
                    }
                }elseif($poi_cvsetting->font_family == 'I'){
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $poipopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf';
                    }else{
                        $poipopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Italic.ttf');
                    }
                }else{
                    if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                        $poipopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
                    }else{
                        $poipopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                    }
                }

                $poipopinsfont = TCPDF_FONTS::addTTFfont($poipopins,'','',32);

                if($poi_cvsetting->font_family == 'none'){
                    $poifonttype = "";
                }else{
                    $poifonttype = $poi_cvsetting->font_family;
                }

                if($poi_cvsetting->font_size != ''){
                    $poifontsize = $poi_cvsetting->font_size;

                }else{
                    $poifontsize = "11";
                }

                PDF::SetFont($poipopinsfont, $poifonttype, $poifontsize,'',false);
                if($poi_cvsetting->font_color != ''){
                    $poifontcolor = explode(",",$poi_cvsetting->font_color);
                    PDF::SetTextColor($poifontcolor[0],$poifontcolor[1],$poifontcolor[2]);
                }else{
                    PDF::SetTextColor(0,0,0);
                }
                PDF::SetXY($poi_cvsetting->x_axis,$poi_cvsetting->y_axis);
                PDF::Cell(0,0,$post->poiname);
            }

            PDF::AddPage();
            // Default fonts set

            if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                $sansarabic = base_path().'/public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Medium.ttf';
                $filecand_pepopins = base_path().'/public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf';
            }else{
                $filecand_pepopins = base_path('public/admin/assets/custom_fonts/poppins/Poppins-Regular.ttf');
                $sansarabic = base_path('public/admin/assets/custom_fonts/IBM_Plex_Sans_Arabic/IBMPlexSansArabic-Medium.ttf');
            }
            // $tajawalfont = TCPDF_FONTS::addTTFfont($tajawal,'','',32);
            $sansarabicfont = TCPDF_FONTS::addTTFfont($sansarabic,'','',32);
            $filecand_pepopinsfont = TCPDF_FONTS::addTTFfont($filecand_pepopins,'','',32);

            if($post->pass_file != ''){
                // Passport
                PDF::SetFont($filecand_pepopinsfont, '', 16);
                PDF::SetXY(10,10);
                PDF::Cell(0,0,"Passport Copy:");

                PDF::SetFont($sansarabicfont, '', 16);
                PDF::SetXY(160,10);
                PDF::Cell(0,0,"صورة جواز السفر:",0,0,'R');

                if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                    $passpath = base_path().'/public/admin/assets/images/candidate/'.$post->pass_file;
                }else{
                    $passpath = base_path('public/admin/assets/images/candidate/'.$post->pass_file);
                }

                $passext = pathinfo($post->pass_file, PATHINFO_EXTENSION);

                PDF::Image($passpath,30,30, '150', '',$passext,'','',false,'300','',false,false,0,false,false,false);
            }

            if($post->lic_file != ''){

                PDF::SetFont($filecand_pepopinsfont, '', 16);
                PDF::SetXY(10,140);
                PDF::Cell(0,0,"Driving Licence:");

                PDF::SetFont($sansarabicfont, '', 16);
                PDF::SetXY(160,140);
                PDF::Cell(0,0,"رخصة قيادة:",0,0,"R");

                if(isset($basepathSt) && $basepathSt->base_path_status == 1){
                    $licpath = base_path().'/public/admin/assets/images/candidate/'.$post->lic_file;
                }else{
                    $licpath = base_path('public/admin/assets/images/candidate/'.$post->lic_file);
                }
                $licext = pathinfo($post->lic_file, PATHINFO_EXTENSION);


                PDF::Image($licpath,30,160, '150', '',$licext,'','',false,'300','',false,false,0,false,false,false);
            }
            ob_end_clean();
            PDF::Output($post->cand_name.'_cv.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('pdfError','Something is wrong please check the CV Text format ');
        }

    }

    public function redirecttowhatsapp($randomstring,$staff) {
        $post = Whatsappchaturl::where('staff_id','=',$staff)->first();

        $sendMessage = "Hello World This is qamarinternational\nMy name is Abdul Kalam Shaikh";
        if ($post) {
            $mobile_no = Admin::find($post->staff_id);
            if ($mobile_no->work_number != '') {
                $mob_no = $mobile_no->work_number;
            } else {
                $mob_no = $mobile_no->phone;
            }


            $whatsapp_chaturl = "https://wa.me/".$mob_no."?".urlencode($sendMessage);
        }else{
            $whatsapp_chaturl = "";
        }




        // dd($whatsapp_chaturl);

        return view('admin.whatsappchaturl.redirect',compact('whatsapp_chaturl'));
    }
}
