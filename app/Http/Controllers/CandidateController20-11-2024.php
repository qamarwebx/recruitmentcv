<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Admin;
use App\Models\Candidate;
use App\Models\Candidatebackupreference;
use App\Models\Candpubst;
use App\Models\City;
use App\Models\Country;
use App\Models\Placeofissue;
use App\Models\Profession;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Carnknown;
use App\Models\Candidatefilterlist;
use App\Models\Candidatefile;
use App\Models\Adminpermission;
use App\Models\Booking;
use App\Models\Bookingpayment;
use App\Models\Visadetails;
use App\Models\Candidatebookinglimit;
use App\Models\Basepathstatus;
use App\Models\Education;
use App\Models\Religion;
use App\Models\Partner;
use App\Models\Companycvexecute;
use App\Models\Associates;
use App\Models\Candsercharge;
use App\Models\Paymentcand;
use App\Models\Expecworkcity;
use App\Models\CandidateStatus;
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

class CandidateController extends Controller
{
    public function index()
    {

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();



        $jobtypes = Profession::orderBy('id','DESC')->get();
        $pois = Placeofissue::orderBy('id','DESC')->get();
        $countries = Country::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $regions = Region::orderBy('name')->get();
        $careoff = Admin::orderBy('name','ASC')->get();
        $filter_user = Candidatefilterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        $associate = Associates::where('id','!=',7)->orderBy('pty_full_name')->get();
        $cand_statuses = CandidateStatus::all();

        $candDBStatusFIlters = CandidateStatus::orderBy('priority')->get();
        $candStatusFilters = DB::table('candidates')->select('candidate_current_status')->groupBy('candidate_current_status')->where('candidate_current_status','!=','')->get();

        $db_fil_status = [];
        $get_fil_status = [];
        foreach($candDBStatusFIlters as $candDBStatusFIlter){
            $get_fil_status [] = $candDBStatusFIlter->status;
        }
        foreach($candStatusFilters as $candStatusFilter){
            $db_fil_status[] = $candStatusFilter->candidate_current_status;
        }

        $final_cand_status = array_intersect($get_fil_status,$db_fil_status);

        $final_db_status = CandidateStatus::wherein('status',$final_cand_status)->orderBy('priority')->get();

       $expworklocs = Expecworkcity::get();
        $dbStatus = [];
        foreach($final_db_status as $dbStatus2){
            $dbStatus[] = $dbStatus2->status;
        }

        // dd($candDBStatusFIlters);

        return view('admin.candidate.index',['jobtypes' =>$jobtypes ,'final_cand_status' => $candDBStatusFIlters,'associates' => $associate,'careoffs' => $careoff,'pois' => $pois,'countries' => $countries,'cities' => $cities,'regions' => $regions,'filter_user' => $filter_user,'perm' => $permission,'expworklocs'=>$expworklocs, 'cand_statuses' =>$cand_statuses]);
    }



    public function indexjson(Request $request)
    {

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $posts = DB::table('candidates as cand')
            ->leftjoin('professions as proff','cand.proff_id','=','proff.id')
            ->leftjoin('admins as admin','cand.admin_id','=','admin.id')
            ->leftjoin('professions as occupation','cand.jobtype_id','occupation.id')
            ->leftjoin('cand_statuses as candST','cand.id','=','candST.cand_id')
            ->leftjoin('admins as careoff','cand.careoff_id','=','careoff.id')
            ->select('cand.*','proff.eng_name as engname','admin.name as uname','careoff.name as careoffname','occupation.eng_name as oengname','occupation.ar_name as oarname','candST.new_candidate','candST.candidate_are_ready','candST.published_for_selection','candST.selected','candST.visa_received','candST.passport_in_embassy','candST.visa_stamped','candST.applied_for_emigration','candST.emigration_approved','candST.waiting_for_flight_ticket','candST.ticket_confirmed','candST.deployed','candST.cancelled','candST.hold','candST.visa_cencelled')
            ->where('cand.isdelete','=',0);

            if($request->has('candidate_current_status') && !empty($request->candidate_current_status)){
                $posts->wherein('cand.candidate_current_status',$request->candidate_current_status);
            }
            if($request->has('careoff') && !empty($request->careoff)){
                $posts->wherein('cand.careoff_id',$request->careoff);
            }
            // start here
            if($request->has('expwp_id') && !empty($request->expwp_id)){
                // $posts->wherein('cand.expwp_id',$request->expwp_id);
                // $posts->whereRaw("FIND_IN_SET(?, cand.expwp_id)",[$request->expwp_id]);

                // Convert expwp_id to an array if it's not already
                $expwpIds = is_array($request->expwp_id) ? $request->expwp_id : [$request->expwp_id];

                // Use whereRaw with FIND_IN_SET for comma-separated values
                $posts->where(function ($query) use ($expwpIds) {
                    foreach ($expwpIds as $id) {
                        $query->orWhereRaw("FIND_IN_SET(?, cand.expwp_id)", [$id]);
                    }
                });
            }

            // end here
            if($request->has('pass_type') && !empty($request->pass_type)){
                $posts->wherein('cand.pass_type',$request->pass_type);
            }
            if($request->has('job_type_id') && !empty($request->job_type_id)){
                $posts->wherein('cand.jobtype_id',$request->job_type_id);
            }
            if($request->has('createby_id') && !empty($request->createby_id)){
                $posts->wherein('cand.admin_id',$request->createby_id);
            }

            if($request->has('religion_id') && !empty($request->religion_id)){
                $posts->wherein('cand.religion_id',$request->religion_id);
            }

            if($request->has('bycity') && !empty($request->bycity)){
                $posts->wherein('cand.candcity_text',$request->bycity);
            }

            if($request->has('region_id') && !empty($request->region_id)){
                $posts->wherein('cand.region_id',$request->region_id);
            }
            if($request->has('experience_region') && !empty($request->experience_region)){
                $posts->wherein('cand.gulfexperience',$request->experience_region);
            }

            if($request->has('publish_status') && !empty($request->publish_status)){
                $posts->wherein('cand.publish',$request->publish_status);
            }

            if($request->has('medical_status') && !empty($request->medical_status)){
                $posts->wherein('cand.medical_health_status',$request->medical_status);
            }
            if($request->has('payment_status') && !empty($request->payment_status)){
                $posts->wherein('cand.cand_payment_status',$request->payment_status);
            }
            if($request->has('medical_expiry_date') && !empty($request->medical_expiry_date)){
                $posts->where('cand.medical_expiry_date','=',$request->medical_expiry_date);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date = $request->get('start_date');
                $end_date = $request->get('end_date');
                if ($start_date && $end_date) {
                    $posts->whereBetween('cand.created_at', [$start_date, $end_date]);
                }
            }


            if ($request->has('source_date_start') && $request->has('source_date_end')) {
                $start_date_s = $request->source_date_start;
                $end_date_s = $request->source_date_end;

                if ($start_date_s && $end_date_s) {
                    $posts->whereBetween('cand.sourcing_date',[$start_date_s,$end_date_s]);
                }

            }

            // if($search = $request->input('search.value')){
            //     $posts->where(function($query) use($search){
            //         $query->orwhere('cand.pass_no','like',"%{$search}%");
            //     });
            // }

            $posts->orderBy('id','DESC')->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_candidate == 1){
                $posts = DB::table('candidates as cand')
                ->leftjoin('professions as proff','cand.proff_id','=','proff.id')
                ->leftjoin('admins as admin','cand.admin_id','=','admin.id')
                ->leftjoin('admins as careoff','cand.careoff_id','=','careoff.id')
                ->leftjoin('professions as occupation','cand.jobtype_id','occupation.id')
                ->leftjoin('cand_statuses as candST','cand.id','=','candST.cand_id')
                // ->select('cand.*','proff.eng_name as engname','admin.name as uname','careoff.name as careoffname')
                ->select('cand.*','proff.eng_name as engname','admin.name as uname','careoff.name as careoffname','occupation.eng_name as oengname','occupation.ar_name as oarname','candST.new_candidate','candST.candidate_are_ready','candST.published_for_selection','candST.selected','candST.visa_received','candST.passport_in_embassy','candST.visa_stamped','candST.applied_for_emigration','candST.emigration_approved','candST.waiting_for_flight_ticket','candST.ticket_confirmed','candST.deployed','candST.cancelled','candST.hold','candST.visa_cencelled')
                ->where('cand.isdelete','=',0);
                // ->where('cand.admin_id','=',Auth::guard('admin')->user()->id)

                if($request->has('candidate_current_status') && !empty($request->candidate_current_status)){
                    $posts->wherein('cand.candidate_current_status',$request->candidate_current_status);
                }
                if($request->has('careoff') && !empty($request->careoff)){
                    $posts->wherein('cand.careoff_id',$request->careoff);
                }
                // if($request->has('expwp_id') && !empty($request->expwp_id)){
                //     $posts->wherein('cand.expwp_id',$request->expwp_id);
                // }
                // start here
                if($request->has('expwp_id') && !empty($request->expwp_id)){
                    // $posts->wherein('cand.expwp_id',$request->expwp_id);
                    // $posts->whereRaw("FIND_IN_SET(?, cand.expwp_id)",[$request->expwp_id]);

                    // Convert expwp_id to an array if it's not already
                    $expwpIds = is_array($request->expwp_id) ? $request->expwp_id : [$request->expwp_id];

                    // Use whereRaw with FIND_IN_SET for comma-separated values
                    $posts->where(function ($query) use ($expwpIds) {
                        foreach ($expwpIds as $id) {
                            $query->orWhereRaw("FIND_IN_SET(?, cand.expwp_id)", [$id]);
                        }
                    });
                }
                if($request->has('pass_type') && !empty($request->pass_type)){
                    $posts->wherein('cand.pass_type',$request->pass_type);
                }
                if($request->has('job_type_id') && !empty($request->job_type_id)){
                    $posts->wherein('cand.jobtype_id',$request->job_type_id);
                }
                if($request->has('createby_id') && !empty($request->createby_id)){
                    $posts->wherein('cand.admin_id',$request->createby_id);
                }

                if($request->has('religion_id') && !empty($request->religion_id)){
                    $posts->wherein('cand.religion_id',$request->religion_id);
                }

                if($request->has('bycity') && !empty($request->bycity)){
                    $posts->wherein('cand.candcity_text',$request->bycity);
                }

                if($request->has('region_id') && !empty($request->region_id)){
                    $posts->wherein('cand.region_id',$request->region_id);
                }
                if($request->has('experience_region') && !empty($request->experience_region)){
                    $posts->wherein('cand.gulfexperience',$request->experience_region);
                }

                if($request->has('publish_status') && !empty($request->publish_status)){
                    $posts->wherein('cand.publish',$request->publish_status);
                }

                if($request->has('medical_status') && !empty($request->medical_status)){
                    $posts->wherein('cand.medical_health_status',$request->medical_status);
                }
                if($request->has('payment_status') && !empty($request->payment_status)){
                    $posts->wherein('cand.cand_payment_status',$request->payment_status);
                }
                if($request->has('medical_expiry_date') && !empty($request->medical_expiry_date)){
                    $posts->where('cand.medical_expiry_date','=',$request->medical_expiry_date);
                }

                if ($request->has('start_date') && $request->has('end_date')) {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');
                    if ($start_date && $end_date) {
                        $posts->whereBetween('cand.created_at', [$start_date, $end_date]);
                    }
                }

                if ($request->has('source_date_start') && $request->has('source_date_end')) {
                    $start_date_s = $request->source_date_start;
                    $end_date_s = $request->source_date_end;

                    if ($start_date_s && $end_date_s) {
                        $posts->whereBetween('cand.sourcing_date',[$start_date_s,$end_date_s]);
                    }

                }

                $posts->orderBy('id','DESC')
                ->get();
            }else{
                $posts = DB::table('candidates as cand')
                ->leftjoin('professions as proff','cand.proff_id','=','proff.id')
                ->leftjoin('admins as admin','cand.admin_id','=','admin.id')
                ->leftjoin('professions as occupation','cand.jobtype_id','occupation.id')
                ->leftjoin('cand_statuses as candST','cand.id','=','candST.cand_id')
                ->leftjoin('admins as careoff','cand.careoff_id','=','careoff.id')
                // ->select('cand.*','proff.eng_name as engname','cand.reference_no as aname','admin.name as uname','careoff.name as careoffname')
                ->select('cand.*','proff.eng_name as engname','admin.name as uname','careoff.name as careoffname','occupation.eng_name as oengname','occupation.ar_name as oarname','candST.new_candidate','candST.candidate_are_ready','candST.published_for_selection','candST.selected','candST.visa_received','candST.passport_in_embassy','candST.visa_stamped','candST.applied_for_emigration','candST.emigration_approved','candST.waiting_for_flight_ticket','candST.ticket_confirmed','candST.deployed','candST.cancelled','candST.hold','candST.visa_cencelled')
                ->where('cand.isdelete','=',0);

                if($request->has('candidate_current_status') && !empty($request->candidate_current_status)){
                    $posts->wherein('cand.candidate_current_status',$request->candidate_current_status);
                }
                if($request->has('careoff') && !empty($request->careoff)){
                    $posts->wherein('cand.careoff_id',$request->careoff);
                }
                // if($request->has('expwp_id') && !empty($request->expwp_id)){
                // $posts->wherein('cand.expwp_id',$request->expwp_id);
                // }
                // start here
                if($request->has('expwp_id') && !empty($request->expwp_id)){
                    // $posts->wherein('cand.expwp_id',$request->expwp_id);
                    // $posts->whereRaw("FIND_IN_SET(?, cand.expwp_id)",[$request->expwp_id]);

                    // Convert expwp_id to an array if it's not already
                    $expwpIds = is_array($request->expwp_id) ? $request->expwp_id : [$request->expwp_id];

                    // Use whereRaw with FIND_IN_SET for comma-separated values
                    $posts->where(function ($query) use ($expwpIds) {
                        foreach ($expwpIds as $id) {
                            $query->orWhereRaw("FIND_IN_SET(?, cand.expwp_id)", [$id]);
                        }
                    });
                }

                if($request->has('pass_type') && !empty($request->pass_type)){
                    $posts->wherein('cand.pass_type',$request->pass_type);
                }
                if($request->has('job_type_id') && !empty($request->job_type_id)){
                    $posts->wherein('cand.jobtype_id',$request->job_type_id);
                }
                if($request->has('createby_id') && !empty($request->createby_id)){
                    $posts->wherein('cand.admin_id',$request->createby_id);
                }

                if($request->has('religion_id') && !empty($request->religion_id)){
                    $posts->wherein('cand.religion_id',$request->religion_id);
                }

                if($request->has('bycity') && !empty($request->bycity)){
                    $posts->wherein('cand.candcity_text',$request->bycity);
                }

                if($request->has('region_id') && !empty($request->region_id)){
                    $posts->wherein('cand.region_id',$request->region_id);
                }
                if($request->has('experience_region') && !empty($request->experience_region)){
                    $posts->wherein('cand.gulfexperience',$request->experience_region);
                }

                if($request->has('publish_status') && !empty($request->publish_status)){
                    $posts->wherein('cand.publish',$request->publish_status);
                }

                if($request->has('medical_status') && !empty($request->medical_status)){
                    $posts->wherein('cand.medical_health_status',$request->medical_status);
                }
                if($request->has('payment_status') && !empty($request->payment_status)){
                    $posts->wherein('cand.cand_payment_status',$request->payment_status);
                }
                if($request->has('medical_expiry_date') && !empty($request->medical_expiry_date)){
                    $posts->where('cand.medical_expiry_date','=',$request->medical_expiry_date);
                }

                if ($request->has('start_date') && $request->has('end_date')) {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');
                    if ($start_date && $end_date) {
                        $posts->whereBetween('cand.created_at', [$start_date, $end_date]);
                    }
                }

                if ($request->has('source_date_start') && $request->has('source_date_end')) {
                    $start_date_s = $request->source_date_start;
                    $end_date_s = $request->source_date_end;

                    if ($start_date_s && $end_date_s) {
                        $posts->whereBetween('cand.sourcing_date',[$start_date_s,$end_date_s]);
                    }

                }


                $posts->orderBy('id','DESC')
                ->where('cand.careoff_id','=',Auth::guard('admin')->user()->id)
                // ->where('cand.admin_id','=',Auth::guard('admin')->user()->id)
                ->get();
            }
        }
        // $posts = Candidate::orderBy('id','DESC')->get();
        // $data['data'] = $posts;
        // return response()->json($data);



        return DataTables::of($posts)->make(true);
        // return DataTables::of($posts)->toJson();
    }
    public function statusChange(Request $request)
    {
        $post = Candidate::find($request->id);
        return response()->json($post);
    }

    public function statusUpdate(Request $request){

        $post =  Candidate::find($request->cand_id);
        $post->cand_status = $request->cand_status;
        $post->save();

        return redirect()->back()->with('success','Status updated!');
    }

    public function store(Request $request)
    {
        $basepathstatus = Basepathstatus::first();

        $random_string = Str::random(16);
        $slug_text = $random_string.''.time();


        // dd($request);
        if($request->dob != ''){
            $age = (date('Y') - date('Y',strtotime($request->dob)));
        }
        // Check reference number is available on Candidate Backup
        $chkrefbk = Candidatebackupreference::count();
        if($chkrefbk > 0){
            $postcbref = Candidatebackupreference::orderBy('reference_no','ASC')->first();
            // $refNo = $postcbref->reference_no;
            if(str_contains($postcbref->reference_no,'RF')){
                $refNo = $postcbref->reference_no;
            }else{
                $refNo = 'RF'.$postcbref->reference_no;
            }
        }else{
            $candCount = Candidate::where('reference_no','!=','')->count();
            $refNo = 'RF'.$candCount + 1;
        }



        // passport file
        if ($request->hasFile('pass_file')) {
            $file = $request->file('pass_file');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            // $repspfilename = str_replace(" ","_",$filename_ren);
            $repspfilename = "PP-".$request->pass_no."-".time();
            $new_file1 = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
                $file->move(base_path().'/public/admin/assets/images/candidate',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file1);
            }

            $pass_file = $new_file1;
        } else {
            $pass_file = '';
        }

        // license file
        if ($request->hasFile('lic_file')) {
            $file = $request->file('lic_file');
            $name2 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren2 = pathinfo($name2,PATHINFO_FILENAME);
            $fileext_ren2 = pathinfo($name2,PATHINFO_EXTENSION);
            // $repspfilename2 = str_replace(" ","_",$filename_ren2);
            $repspfilename2 = "DL-".$request->pass_no."-".time();
            $new_file2 = $repspfilename2.'.'.$fileext_ren2;
            if($basepathstatus->base_path_status == 1){
                $file->move(base_path().'/public/admin/assets/images/candidate',$new_file2);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file2);
            }

            $lic_file = $new_file2;
        } else {
            $lic_file = '';
        }
        // CV file
        if ($request->hasFile('cv_file')) {
            $file = $request->file('cv_file');
            $name3 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren3 = pathinfo($name3,PATHINFO_FILENAME);
            $fileext_ren3 = pathinfo($name3,PATHINFO_EXTENSION);
            // $repspfilename3 = str_replace(" ","_",$filename_ren3);
            $repspfilename3 = "FP-".$request->pass_no."-".time();
            $new_file3 = $repspfilename3.'.'.$fileext_ren3;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file3);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file3);
            }
            $cv_file = $new_file3;
        } else {
            $cv_file = '';
        }
        // Photo file
        if ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $name4 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren4 = pathinfo($name4,PATHINFO_FILENAME);
            $fileext_ren4 = pathinfo($name4,PATHINFO_EXTENSION);
            // $repspfilename4 = str_replace(" ","_",$filename_ren4);
            $repspfilename4 = "P-".$request->pass_no."-".time();
            $new_file4 = $repspfilename4.'.'.$fileext_ren4;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file4);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file4);
            }

            $photo_file = $new_file4;
        } else {
            $photo_file = '';
        }

        $post = new Candidate();
        $post->cand_name = $request->cand_name;
        $post->arcand_name = $request->arcand_name;
        $post->pass_no = $request->pass_no;
        $post->pass_type = $request->pass_type;
        $post->doi = date('Y-m-d',strtotime($request->doi));
        $post->doe = date('Y-m-d',strtotime($request->doe));
        $post->dob = date('Y-m-d',strtotime($request->dob));
        $post->poi = $request->poi;
        $post->sourcing_date = $request->sourcing_date;
        // $post->poi_text = $request->poi_text;
        // $post->experience = implode(',',$request->experience);
        // $post->expcountry_id = implode(',',$request->expcountry_id);
        // $post->expcity_id = implode(',',$request->expcity_id);
        $post->jobtype_id = $request->jobtype_id;
        // $post->proff_id = implode(',',$request->proff_id);
        // $post->age = $age;
        // $post->religion = $request->religion;
        if($request->dob != ''){
            $post->age = (date('Y') - date('Y',strtotime($request->dob)));
        }
        $post->contact_no = $request->contact_no;
        $post->marital_status = $request->marital_status;
        if($request->marital_status == 'Married'){
            $post->ar_marital_status = 'متزوج';
        }
        if($request->marital_status == 'Unmarried'){
            $post->ar_marital_status = 'غير متزوج';
        }
        // $post->lang_known = implode(',',$request->lang_known);
        // $post->exp_sal = $request->exp_sal;
        $post->mobile_no = $request->mobile_no;
        $post->address = $request->address;
        $post->pass_file = $pass_file;
        $post->lic_file = $lic_file;
        $post->cv_file = $cv_file;
        $post->photo_file = $photo_file;
        $post->admin_id = Auth::guard('admin')->user()->id;
        // $post->overall_exp = array_sum($request->experience);

        $post->nation_id = $request->nation_id;
        // $post->region_id = $request->region_id;
        // $post->candcity_id = $request->candcity_id;
        // $post->plb_id = $request->plb_id;
        $post->plb_text = $request->plb_text;
        // $post->google_map = $request->google_map;
        // if($request->expwp_id != ''){
        //     $post->expwp_id = implode(",",$request->expwp_id);
        // }
        $post->cand_status = '1';
        $post->reference_no = $refNo;
        $post->careoff_id = $request->careoff_id;
        $post->associate_id = $request->associate_id;
        $post->candidate_current_status = "New Candidate";
        $post->slug_text = $slug_text;
        $post->save();

        $get_last_id = $post->id;

        // Update Public ST

        $postPB = new Candpubst();
        $postPB->cand_id = $get_last_id;
        $postPB->stage_name = 'Passport Stage';
        $postPB->admin_id = Auth::guard('admin')->user()->id;
        $postPB->save();

        // delete Check backup ref no
        $delRef = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();

        // Add or Update Candidate Status
        $candStPost = new CandStatus();
        $candStPost->cand_id = $post->id;
        $candStPost->new_candidate = true;
        $candStPost->Save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $post->id;
        $timeline->headline = "New candidate created";
        $timeline->bodyMessage = "New candidate created by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A',strtotime($post->created_at));
        $timeline->icons = "ti ti-user-check";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        return redirect()->back()->with('success','Candidate created!');

    }

    public function show($id)
    {
        //dd($id);
        $post = Candidate::find($id);
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


        $users = Admin::where('id','!=',1)->orderBy('name','ASC')->get();

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
        $associates = Associates::orderBy('pty_full_name')->where('careoff_id','=',Auth::guard('admin')->user()->user_id)->where('id','!=',7)->get();
        $careoffs = Admin::orderBy('name')->get();
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

        $activeService = Candsercharge::where('cand_id','=',$id)->where('status','=',1)->first();

        if($activeService){
            $PaytotalAmount = 0;
            $ServiveAmount = $activeService->amount;
            foreach($paycands as $paycand)
            {
                $PaytotalAmount += $paycand->amount;
            }
            $CalAmount = ($ServiveAmount - $PaytotalAmount);

        }else {
            // Display a message indicating no records
            //echo "No active service records found.";
            $PaytotalAmount = 0;
            foreach($paycands as $paycand)
            {
                $PaytotalAmount += $paycand->amount;
            }
            $CalAmount = 0;
        }
        // $examinDte = time();
        // $expiryDte = strtotime($post->medical_expiry_date);
        // $diffDte = $expiryDte - $examinDte;
        // $days = round($diffDte / (60 * 60 *24));
        // dd($diffDte);

        $permission = DB::table('adminpermissions')->where('staff_id','=',Auth::guard('admin')->user()->id)->first();

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

        return view('admin.candidate.show',compact('careoffName','reminders','todolabels','permission','vehtrans','myVehtr','post','expworkcities','expworklocs','candsc','careoffs','associates','partners','paycands','occupation','educationname','educations','religions','religionname','candDownloads','candcount','candBooks','candcity','filePhotos','myCarkn','cars','timelines','placeofbirth','nation','candRegion','poiss','myexpwl','cities','countries','regions','jobtypes','poi','admin','mycont','proffs','myproff','mycity','users','CalAmount','PaytotalAmount','activeService','embassies'));
    }


    public function candreminderstore(Request $request){
        // Store Reminder in Candidate Reminder
        $post = new Candidatereminder();
        $post->candidate_id = $request->candidateID;
        $post->title = $request->title;
        $post->desc = $request->desc;
        $post->todolabel_id = $request->todolabel_id;
        $post->due_date = $request->due_date;
        $post->time = $request->time;
        $post->careoff_id = $request->careoff_id;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        // Update in Todo
        $postTodo = new Todo();
        $postTodo->task_title = $request->title;
        $postTodo->task_description = $request->desc;
        $postTodo->assignto_id = $request->careoff_id;
        $postTodo->todolabel_id = $request->todolabel_id;
        $postTodo->start_on = date('Y-m-d');
        $postTodo->finish_on = $request->due_date;
        $postTodo->reminder_cycle = "Low";
        $postTodo->task_status = "New Task";
        $postTodo->admin_id = Auth::guard('admin')->user()->id;
        $postTodo->save();

        return redirect()->back()->with('success','Reminder Set!');

    }

    public function candreminderEdit(Request $request) {
        $post = Candidatereminder::find($request->id);

        return response()->json($post);
    }

    public function candreminderUpdate(Request $request){
        if ($request->new_reminder == 'Required') {
            $post = new Candidatereminder();
            $post->candidate_id = $request->candID;
            $post->title = $request->title;
            $post->desc = $request->desc;
            $post->todolabel_id = $request->todolabel_id;
            $post->due_date = $request->due_date;
            $post->time = $request->time;
            $post->careoff_id = $request->careoff_id;
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();

            // Inactive Previous Reminder
            $prepost = Candidatereminder::find($request->reminder_id);
            $prepost->status = false;
            $prepost->save();

            // Update in Todo
            $postTodo = new Todo();
            $postTodo->task_title = $request->title;
            $postTodo->task_description = $request->desc;
            $postTodo->assignto_id = $request->careoff_id;
            $postTodo->todolabel_id = $request->todolabel_id;
            $postTodo->start_on = date('Y-m-d');
            $postTodo->finish_on = $request->due_date;
            $postTodo->reminder_cycle = "Low";
            $postTodo->task_status = "New Task";
            $postTodo->admin_id = Auth::guard('admin')->user()->id;
            $postTodo->save();

            return redirect()->back()->with('success','New reminder created!');
        }else{
            $updatePost = Candidatereminder::find($request->reminder_id);
            $updatePost->title = $request->title;
            $updatePost->desc = $request->desc;
            $updatePost->save();

            return redirect()->back()->with('success','Reminder updated!');
        }
    }


    public function videoLinkStore(Request $request,$id){
        $post = Candidate::find($id);
        $post->video_link = $request->video_link;
        $post->save();

        return redirect()->back()->with('success','Video link uploaded!');
    }
    public function testvideoLinkStore(Request $request,$id){
        $post = Candidate::find($id);
        $post->trade_test_video_link = $request->trade_test_video_link;
        $post->save();

        return redirect()->back()->with('success','Test Video link uploaded!');
    }
   /*  public function videoFileStore(Request $request, $id)
    {
        $request->validate([
            'video_file' => 'required',
        ]);

        $videoName = $request->file('video_file')->store('videos');

        $post = Candidate::find($id);
        $post->video_file = $videoName;
        $post->save();

        return redirect()->back()->with('success', 'Video uploaded successfully!');
    } */
    public function videoFileStore(Request $request, $id)
    {
        $request->validate([
            'video_file' => 'required|mimes:mp4,mov,avi|max:10240', // Adjust max size as needed
        ]);

        $video = $request->file('video_file');
        $videoName = time() . '_' . $video->getClientOriginalName();
        $video->move(public_path('videos'), $videoName);
        ///dd($videoName);

        $post = Candidate::find($id);
        $post->video_file = $videoName;
        $post->save();
        //dd($post);

        return redirect()->back()->with('success', 'Video uploaded successfully!');
    }
    public function edit(Request $request)
    {
        $post = Candidate::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request)
    {
        $basepathstatus = Basepathstatus::first();
        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();
        $checkCandStatus = CandStatus::where('cand_id','=',$id)->count();

        if($post->slug_text == ''){
            $random_string = Str::random(16);
            $slug_text = $random_string.''.time();

            $post->slug_text = $slug_text;
        }



        // check reference number is alloted or not
        if($post->reference_no != ''){

            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }


        //dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }

        // passport file
        if ($request->hasFile('pass_file')) {
            $file = $request->file('pass_file');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
            // $repspfilename = str_replace(" ","_",$filename_ren1);
            $repspfilename = "PP-".$request->pass_no."-".time();
            $new_file1 = $repspfilename.'.'.$fileext_ren1;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file1);
            }

            $pass_file = $new_file1;
        } else {
            $pass_file = $post->pass_file;
        }

        // license file
        if ($request->hasFile('lic_file')) {
            $file = $request->file('lic_file');
            $name2 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren2 = pathinfo($name2,PATHINFO_FILENAME);
            $fileext_ren2 = pathinfo($name2,PATHINFO_EXTENSION);
            // $repspfilename2 = str_replace(" ","_",$filename_ren2);
            $repspfilename2 = "DL-".$request->pass_no."-".time();
            $new_file2 = $repspfilename2.'.'.$fileext_ren2;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file2);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file2);
            }
            $lic_file = $new_file2;
        } else {
            $lic_file = $post->lic_file;
        }
        // CV file
        if ($request->hasFile('cv_file')) {
            $file = $request->file('cv_file');
            $name3 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren3 = pathinfo($name3,PATHINFO_FILENAME);
            $fileext_ren3 = pathinfo($name3,PATHINFO_EXTENSION);
            // $repspfilename3 = str_replace(" ","_",$filename_ren3);
            $repspfilename3 = "FP-".$request->pass_no."-".time();
            $new_file3 = $repspfilename3.'.'.$fileext_ren3;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file3);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file3);
            }

            $cv_file = $new_file3;
        } else {
            $cv_file = $post->cv_file;
        }
        // Photo file
        if ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $name4 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren4 = pathinfo($name4,PATHINFO_FILENAME);
            $fileext_ren4 = pathinfo($name4,PATHINFO_EXTENSION);
            // $repspfilename4 = str_replace(" ","_",$filename_ren4);
            $repspfilename4 = "P-".$request->pass_no."-".time();
            $new_file4 = $repspfilename4.'.'.$fileext_ren4;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file4);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file4);
            }

            $photo_file = $new_file4;
        } else {
            $photo_file = $post->photo_file	;
        }

        $post->cand_name = $request->cand_name;
        $post->arcand_name = $request->arcand_name;
        $post->pass_no = $request->pass_no;
        $post->pass_type = $request->pass_type;
        $post->doi = date('Y-m-d',strtotime($request->doi));
        $post->doe = date('Y-m-d',strtotime($request->doe));
        $post->dob = date('Y-m-d',strtotime($request->dob));
        $post->sourcing_date = $request->sourcing_date;

        $post->poi = $request->poi;
        // $post->poi_text = $request->poi_text;

        // if($request->experience != ''){
        //     $post->experience = implode(',',$request->experience);
        // }

        // if($request->expcountry_id != ''){
        //     $post->expcountry_id = implode(',',$request->expcountry_id);
        // }

        // if($request->expcity_id != ''){
        //     $post->expcity_id = implode(',',$request->expcity_id);
        // }

        // if($request->expcity_id_text != ''){
        //     $post->expcity_id_text = implode(',',$request->expcity_id_text);
        // }

        // if($request->proff_id != ''){
        //     $post->proff_id = implode(',',$request->proff_id);
        // }

        // $post->careoff_id = $request->careoff_id;

        $post->jobtype_id = $request->jobtype_id;
        // $post->age = $request->age;
        // $post->religion = $request->religion;
        if($request->dob != ''){
            $post->age = (date('Y') - date('Y',strtotime($request->dob)));
        }
        // $post->religion_id = $request->religion_id;
        $post->contact_no = $request->contact_no;
        $post->marital_status = $request->marital_status;

        if($request->marital_status == 'Married'){
            $post->ar_marital_status = 'متزوج';
        }
        if($request->marital_status == 'Unmarried'){
            $post->ar_marital_status = 'غير متزوج';
        }

        // if($request->lang_known != ''){
        //     $post->lang_known = implode(',',$request->lang_known);
        //     $langs = $request->lang_known;

        //     $arlang = [];
        //     foreach($langs as $lang){
        //         if($lang == 'English'){
        //             $arlang [] = 'إنجليزي';
        //         }

        //         if($lang == 'Hindi'){
        //             $arlang [] = 'الهندية';
        //         }

        //         if($lang == 'Urdu'){
        //             $arlang [] = 'أوردو';
        //         }

        //         if($lang == 'Arabic'){
        //             $arlang [] = 'عربي';
        //         }

        //     }

        //     $post->ar_language = implode(",",$arlang);

        // }
        // if($request->experience != ''){
        //     $post->overall_exp = array_sum($request->experience);
        // }
        // $post->exp_sal = $request->exp_sal;
        // $post->mobile_no = $request->mobile_no;
        $post->address = $request->address;
        $post->pass_file = $pass_file;
        $post->lic_file = $lic_file;
        $post->cv_file = $cv_file;
        $post->photo_file = $photo_file;

        $post->nation_id = $request->nation_id;
        // $post->region_id = $request->region_id;
        // $post->candcity_id = $request->candcity_id;
        // $post->plb_id = $request->plb_id;
        // $post->candcity_text = $request->candcity_text;
        $post->plb_text = $request->plb_text;
        // $post->google_map = $request->google_map;
        // if($request->expwp_id != ''){
        //     $post->expwp_id = implode(",",$request->expwp_id);
        // }

        $post->reference_no = $refNo;
        // $post->associate_id = $request->associate_id;

        // $post->gulfexperience = $request->gulfexperience;
        // if($request->carknown_id != ''){
        //     $post->carknown_id = implode(",",$request->carknown_id);
        // }else{
        //     $post->carknown_id = "";
        // }

        // Update Candidate Status
        if($checkCandStatus == 0){
            $newCheckPost = new CandStatus();
            $newCheckPost->cand_id = $id;
            $newCheckPost->new_candidate = true;
            $newCheckPost->Save();

            $post->candidate_current_status = "New Candidate";
        }

        $post->save();

        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate data updated!";
        $timeline->bodyMessage = "Candidate data updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        // Check candidate data is available for CV executing or Not

        $updCvexectue = Candidate::find($post->id);

        // Change status and remove files of Company CV related to Candidate
        $compcvexs = Companycvexecute::where('cand_id','=',$post->id)->get();

        if(
            $post->cand_name != '' &&
            $post->photo_file != '' &&
            $post->exp_sal != '' &&
            $post->expwp_id != '' &&
            $post->marital_status != '' &&
            $post->religion_id != '' &&
            $post->dob != '' &&
            // $post->plb_id != '' &&
            $post->plb_text != '' &&
            $post->nation_id != '' &&
            $post->region_id != '' &&
            $post->pass_no != '' &&
            $post->pass_type != '' &&
            $post->doi != '' &&
            $post->doe != '' &&
            $post->poi != '' &&
            // $post->poi_text != '' &&
            $post->pass_file != '' &&
            $post->lic_file != '' &&
            $post->gulfexperience != '' &&
            $post->jobtype_id != ''
        ){
            // update cv execute
            $updCvexectue->cv_execute = true;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }

            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }


            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

        }else{
            // update cv execute
            $updCvexectue->cv_execute = false;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }
            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

        }
        $updCvexectue->save();



        return redirect()->back()->with('success','Candidate data updated!');

    }

    public function candscharge(Request $request,$id){
        $post =  Candsercharge::find($request->cand_id);
        $existingActiveRecord =  Candsercharge::where('cand_id','=',$id)->where('status','=',1)->first();
        $checkCandRelSC = Candsercharge::where('cand_id','=',$id)->count();
        //     if(isset($post)){
        //         $post->given_by = $request->given_by;
        //         $post->amount = $request->amount;
        //         $post->edited_at = Carbon::now();
        //         $post->save();
        //     }else{
        //         if($existingActiveRecord){
        //             $existingActiveRecord->update(['status' => 0, 'edited_at' => Carbon::now()]);
        //         }
        //         $newPost = new Candsercharge();
        //         $newPost->cand_id = $id;
        //         $newPost->given_by = $request->given_by;
        //         $newPost->amount = $request->amount;
        //         $newPost->status = $request->status;
        //         $newPost->admin_id = Auth::guard('admin')->user()->id;
        //         $newPost->edited_at = Carbon::now(); // Set the timestamp when creating
        //         $newPost->save();
        //    }

        if(isset($post)){
            $post->given_by = $request->given_by;
            $post->amount = $request->amount;
            $post->notes = $request->notes;
            $post->edited_at = Carbon::now();
            $post->save();

            // Update Payment Status
            $candPayment = Paymentcand::where('cand_id','=',$id)->get();
            $paymentTotal = 0;
            $serviceAmount = $post->amount;

            foreach($candPayment as $candPayment2){
                $paymentTotal += $candPayment2->amount;
            }
            $finalAmount = ($serviceAmount - $paymentTotal);
            $candStatus = Candidate::find($id);
            if($finalAmount > 0){
                $candStatus->cand_payment_status = "Partial Paid";
            }else{
                $candStatus->cand_payment_status = "Paid";
            }
            $candStatus->save();


        }else{
            $newPost = new Candsercharge();
            $newPost->cand_id = $id;
            $newPost->given_by = $request->given_by;
            $newPost->amount = $request->amount;
            $newPost->admin_id = Auth::guard('admin')->user()->id;
            if($checkCandRelSC == 0){
                $newPost->status = true;
            }
            $newPost->notes = $request->notes;
            $newPost->edited_at = Carbon::now();
            $newPost->save();
        }


        // return redirect()->back()->with('success','Service Charge updated!');
        return response()->json(['success' => 'Service Charge updated!']);
    }

    public function SercandamtEdit(Request $request){
        $post = Candsercharge::find($request->id);

        return response()->json($post);
    }

    public function candamtStr(Request $request,$id){
        $basepathstatus = Basepathstatus::first();

        if ($request->hasFile('payment_slip')) {
            $file = $request->file('payment_slip');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren1);
            $new_file1 = $repspfilename.'.'.$fileext_ren1;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/payment/candidate',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/payment/candidate',$new_file1);
            }

            $payment_slip = $new_file1;
        } else {
            $payment_slip = '';
        }

        $post = new Paymentcand();
        $post->cand_id = $id;
        $post->amount = $request->amount;
        $post->payment_mode = $request->payment_mode;
        $post->txn_id = $request->txn_id;
        $post->bank_to = $request->bank_to;
        $post->payment_slip = $payment_slip;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        // Update Payment Status
        $serviceCharge = Candsercharge::where('cand_id','=',$id)->where('status','=',1)->first();
        $candPayment = Paymentcand::where('cand_id','=',$id)->get();


        $paymentTotal = 0;
        $serviceAmount = $serviceCharge->amount;

        foreach($candPayment as $candPayment2){
            $paymentTotal += $candPayment2->amount;
        }
        $finalAmount = ($serviceAmount - $paymentTotal);
        $candStatus = Candidate::find($id);
        if($finalAmount > 0){
            $candStatus->cand_payment_status = "Partial Paid";
        }else{
            $candStatus->cand_payment_status = "Paid";
        }
        $candStatus->save();
        // return redirect()->back()->with('success','Payment added!');
        return response()->json(['success' => 'Payment added!']);
    }

    public function candamtEdit(Request $request){
        $post = Paymentcand::find($request->id);

        return response()->json($post);
    }


    public function candamtUpdt(Request $request){
        $post = Paymentcand::find($request->paycand_id);
        $basepathstatus = Basepathstatus::first();

        if ($request->hasFile('payment_slip')) {
            $file = $request->file('payment_slip');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren1);
            $new_file1 = $repspfilename.'.'.$fileext_ren1;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/payment/candidate',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/payment/candidate',$new_file1);
            }

            $payment_slip = $new_file1;
        } else {
            $payment_slip = $post->payment_slip;
        }

        $post->amount = $request->amount;
        $post->payment_mode = $request->payment_mode;
        $post->txn_id = $request->txn_id;
        $post->bank_to = $request->bank_to;
        $post->payment_slip = $payment_slip;
        $post->save();

        // Update Payment Status
        $serviceCharge = Candsercharge::where('cand_id','=',$post->cand_id)->where('status','=',1)->first();
        $candPayment = Paymentcand::where('cand_id','=',$post->cand_id)->get();


        $paymentTotal = 0;
        if(isset($serviceCharge)){
            $serviceAmount = $serviceCharge->amount;
        }else{
            $serviceAmount = 0;

        }



        foreach($candPayment as $candPayment2){
            $paymentTotal += $candPayment2->amount;
        }
        $finalAmount = ($serviceAmount - $paymentTotal);
        $candStatus = Candidate::find($post->cand_id);
        if($finalAmount > 0){
            $candStatus->cand_payment_status = "Partial Paid";
        }else{
            $candStatus->cand_payment_status = "Paid";
        }
        $candStatus->save();

        // return redirect()->back()->with('success','Payment updated!');
        return response()->json(['success' => 'Payment updated!']);
    }

    public function deleteAmt(Request $request){
        $basepathstatus = Basepathstatus::first();
        $post = Paymentcand::find($request->id);

        if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
            $filepath = base_path('public/admin/assets/images/payment/candidate/'.$post->payment_slip);
        }else{
            $filepath = base_path('public_html/admin/assets/images/payment/candidate/'.$post->payment_slip);
        }

        if(file_exists($filepath)){
            File::delete($filepath);
            $post->delete();
        }else{
            $post->delete();
        }

        // Update Payment Status
        $serviceCharge = Candsercharge::where('cand_id','=',$post->cand_id)->where('status','=',1)->first();
        $candPayment = Paymentcand::where('cand_id','=',$post->cand_id)->get();


        $paymentTotal = 0;
        if(isset($serviceCharge)){
            $serviceAmount = $serviceCharge->amount;
        }else{
            $serviceAmount = 0;
        }


        foreach($candPayment as $candPayment2){
            $paymentTotal += $candPayment2->amount;
        }
        $finalAmount = ($serviceAmount - $paymentTotal);
        $candStatus = Candidate::find($post->cand_id);
        if($finalAmount > 0){
            $candStatus->cand_payment_status = "Partial Paid";
        }else{
            $candStatus->cand_payment_status = "Paid";
        }
        $candStatus->save();
    }

    public function deleteSerAmt(Request $request){
        $basepathstatus = Basepathstatus::first();
        $post = Candsercharge::find($request->id);
        $post->delete();
    }

    public function deactiveServiceCharge(Request $request){
        // $post = Candsercharge::find($request->id);
        $post = Candsercharge::find($request->candser_id);
        $post->status = false;
        $post->approval_notes = $request->approval_notes;
        $post->save();


        return response()->json(['success' => 'Service charge deactived!']);
    }

    public function activeServiceCharge(Request $request){
        // $post = Candsercharge::find($request->id);
        $post = Candsercharge::find($request->candser_id2);
        $post->approval_notes = $request->approval_notes;
        $post->status = true;
        $post->save();

        // Deactive Other Service Charge
        // $candCount = Candsercharge::where('cand_id','=',$post->cand_id)->where('id','!=',$request->id)->get();
        $candCount = Candsercharge::where('cand_id','=',$post->cand_id)->where('id','!=',$request->candser_id2)->get();
        if($candCount->count() > 0){
            foreach($candCount as $candCount){
                $candCount->status = false;
                $candCount->save();
            }
        }


        // Update Payment Status as per paymnet record
        $payment = Paymentcand::where('cand_id','='.$post->cand_id)->get();
        $cand = Candidate::find($post->cand_id);
        if($payment->count() > 0){
            $serviceCharge = $post->amount;
            $payTotal = 0;
            foreach($payment as $payment){
                $payTotal += $payment->amount;
            }
            $CalAmount = ($serviceCharge - $payTotal);
            if($CalAmount == 0){
                $cand->cand_payment_status = "Paid";
            }else{
                $cand->cand_payment_status = "Partial Paid";
            }
        }else{
            $cand->cand_payment_status = "Unpaid";
        }

        $cand->save();

        return response()->json(['success' => 'Service charge actived!']);
    }

    public function checkTxn(Request $request){
        $txn_id = $request->txn_id;

        if($request->id != ''){
            $post = Paymentcand::where('txn_id','=',$txn_id)->where('id','!=',$request->id)->count();

            // if($post == 0){
            //     $isAvailable = 'true';
            // }else{
            //     $isAvailable = 'false';
            // }

            // echo json_encode(array(
            //     'valid' => $isAvailable,
            // ));

            if($post == 0){
                echo "true";
            }else{
                echo "false";
            }

        }else{
            $post = Paymentcand::where('txn_id','=',$txn_id)->count();

            // if($post == 0){
            //     $isAvailable = 'true';
            // }else{
            //     $isAvailable = 'false';
            // }

            // echo json_encode(array(
            //     'valid' => $isAvailable,
            // ));

            if($post == 0){
                echo "true";
            }else{
                echo "false";
            }
        }


    }

    public function careoffupdate(Request $request){
        $post = Candidate::find($request->editID);
        $post->careoff_id = $request->careoff_id;
        $post->associate_id = $request->associate_id;
        $post->sourcing_date = $request->sourcing_date;
        $post->save();

        return response()->json(['success' => 'Candidate careoff updated!']);
    }

    public function assocupdate(Request $request){
        $post = Candidate::find($request->editID);
        $post->associate_id = $request->associate_id;
        $post->save();

        return response()->json(['success' => 'Candidate Associate updated!']);
    }

    public function detailsupdate(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();

        $basepathstatus = Basepathstatus::first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            $refNo = $post->reference_no;
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                $refNo = $refnumbc->reference_no;
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = $countPostref + 1;
            }

        }

        // dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }


        // $post->cand_name = $request->cand_name;
        // $post->pass_no = $request->pass_no;
        // $post->pass_type = $request->pass_type;
        // $post->doi = $request->doi;
        // $post->doe = $request->doe;
        // $post->dob = $request->dob;
        // $post->poi = $request->poi;
        // $post->jobtype_id = $request->jobtype_id;
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        $post->contact_no = $request->contact_no;
        // $post->marital_status = $request->marital_status;
        $post->mobile_no = $request->mobile_no;
        // $post->address = $request->address;
        // $post->nation_id = $request->nation_id;
        // $post->plb_id = $request->plb_id;
        $post->email = $request->email;
        $post->reference_no = $refNo;
        $post->relative_contact_no = $request->relative_contact_no;
        $post->save();

        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate details updated!";
        $timeline->bodyMessage = "Candidate details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        // Check candidate data is available for CV executing or Not

        $updCvexectue = Candidate::find($post->id);

        // Change status and remove files of Company CV related to Candidate
        $compcvexs = Companycvexecute::where('cand_id','=',$post->id)->get();

        if(
            $post->cand_name != '' &&
            $post->photo_file != '' &&
            $post->exp_sal != '' &&
            $post->expwp_id != '' &&
            $post->marital_status != '' &&
            $post->religion_id != '' &&
            $post->dob != '' &&
            // $post->plb_id != '' &&
            $post->plb_text != '' &&
            $post->nation_id != '' &&
            $post->region_id != '' &&
            $post->pass_no != '' &&
            $post->pass_type != '' &&
            $post->doi != '' &&
            $post->doe != '' &&
            $post->poi != '' &&
            // $post->poi_text != '' &&
            $post->pass_file != '' &&
            $post->lic_file != '' &&
            $post->gulfexperience != '' &&
            $post->jobtype_id != '' &&
            $post->mobile_no != '' &&
            $post->contact_no != '' &&
            $post->relative_contact_no != '' &&
            $post->email != ''
        ){
            // update cv execute
            $updCvexectue->cv_execute = true;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }

            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

            // Update Publish Status
            $updCvexectue->publish = true;

        }else{
            // update cv execute
            $updCvexectue->cv_execute = false;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }
            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

            // Update Publish Status
            $updCvexectue->publish = false;

        }
        $updCvexectue->save();


        // return redirect()->back()->with('success','Candidate details updated!');

        return response()->json(['success' => 'Candidate details updated!']);

    }

    public function musanedupdate(Request $request){
        $post = Candidate::find($request->editMusanedID);
        $basepathstatus = Basepathstatus::first();
        if ($request->hasFile('musaned_file')) {
            $file = $request->file('musaned_file');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
            // $repspfilename = str_replace(" ","_",$filename_ren);
            $repspfilename = "MUSANED".$post->pass_no."-".time();
            $new_file1 = $repspfilename.'.'.$fileext_ren;
            if($basepathstatus->base_path_status == 1){
                $file->move(base_path().'/public/admin/assets/images/candidate',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file1);
            }

            $pass_file = $new_file1;
        } else {
            $pass_file = $post->musaned_file;
        }

        $post->musaned_status = $request->musaned_status;
        if($request->musaned_reg_date != ''){
            $post->musaned_reg_date = date('Y-m-d',strtotime($request->musaned_reg_date));
        }
        $post->musaned_file = $pass_file;
        $post->save();

        // return redirect()->back()->with('success','Candidate musaned updated!');
        return response()->json(['success' => 'Candidate Musaned updated!']);
    }
    public function embassyupdate(Request $request)
    {
        dd($request);
        $post = Candidate::find($request->editEmbassyID);
        $post->embassy_for = $request->embassy;
        $post->save();

        return redirect()->back()->with('success','Embassy updated Successfully!');
    }

    public function personalupdate(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();
        $basepathstatus = Basepathstatus::first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }

        // dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }

        // Update Age

        // $post->religion = $request->religion;
        $post->cand_name = $request->cand_name;
        $post->arcand_name = $request->arcand_name;
        $post->marital_status = $request->marital_status;

        if($request->marital_status == 'Married'){
            $post->ar_marital_status = 'متزوج';
        }
        if($request->marital_status == 'Unmarried'){
            $post->ar_marital_status = 'غير متزوج';
        }

        $post->jobtype_id = $request->jobtype_id;
        $post->religion_id = $request->religion_id;
        if($request->lang_known != ''){
            $post->lang_known = implode(',',$request->lang_known);

            $langs = $request->lang_known;
            $arlang = [];
            foreach($langs as $lang){
                if($lang == 'English'){
                    $arlang [] = 'إنجليزي';
                }

                if($lang == 'Hindi'){
                    $arlang [] = 'الهندية';
                }

                if($lang == 'Urdu'){
                    $arlang [] = 'أوردو';
                }

                if($lang == 'Arabic'){
                    $arlang [] = 'عربي';
                }

            }

            $post->ar_language = implode(",",$arlang);

        }
        $post->exp_sal = $request->exp_sal;
        $post->region_id = $request->region_id;
        // $post->candcity_id = $request->candcity_id;
        $post->candcity_text = $request->candcity_text;
        $post->reference_no = $refNo;
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        if($request->expwp_id != ''){
            $post->expwp_id = implode(",",$request->expwp_id);
        }

        // $post->age = $request->age;

        $post->education_id = $request->education_id;
        $post->embassy_for = $request->embassy;
        $post->save();


        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate personal detail updated!";
        $timeline->bodyMessage = "Candidate personal details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        // Check candidate data is available for CV executing or Not

        $updCvexectue = Candidate::find($post->id);

        // Change status and remove files of Company CV related to Candidate
        $compcvexs = Companycvexecute::where('cand_id','=',$post->id)->get();

        if(
            $post->cand_name != '' &&
            $post->photo_file != '' &&
            $post->exp_sal != '' &&
            $post->expwp_id != '' &&
            $post->marital_status != '' &&
            $post->religion_id != '' &&
            $post->dob != '' &&
            // $post->plb_id != '' &&
            $post->plb_text != '' &&
            $post->nation_id != '' &&
            $post->region_id != '' &&
            $post->pass_no != '' &&
            $post->pass_type != '' &&
            $post->doi != '' &&
            $post->doe != '' &&
            $post->poi != '' &&
            // $post->poi_text != '' &&
            $post->pass_file != '' &&
            $post->lic_file != '' &&
            $post->gulfexperience != '' &&
            $post->jobtype_id != '' &&
            $post->mobile_no != '' &&
            $post->contact_no != '' &&
            $post->relative_contact_no != '' &&
            $post->email != ''
        ){
            // update cv execute
            $updCvexectue->cv_execute = true;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }

            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

            // Update Publish Status
            $updCvexectue->publish = true;

        }else{
            // update cv execute
            $updCvexectue->cv_execute = false;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }
            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

            // Update Publish Status
            $updCvexectue->publish = false;

        }
        $updCvexectue->save();

        // return redirect()->back()->with('success','Candidate personal details updated!');

        return response()->json(['success' => 'Candidate personal details updated!']);
    }

    public function passportupdate(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();

        $basepathstatus = Basepathstatus::first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }

        // dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }

        $post->dob = date('Y-m-d',strtotime($request->dob));
        $post->address = $request->address;
        $post->nation_id = $request->nation_id;
        // $post->plb_id = $request->plb_id;
        $post->plb_text = $request->plb_text;

        $post->pass_no = $request->pass_no;
        $post->pass_type = $request->pass_type;
        $post->doi = date('Y-m-d',strtotime($request->doi));
        $post->doe = date('Y-m-d',strtotime($request->doe));
        $post->poi = $request->poi;
        // $post->poi_text = $request->poi_text;
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        $post->reference_no = $refNo;
        $post->embassy_for = $request->embassy;
        $post->save();

        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate passport detail updated!";
        $timeline->bodyMessage = "Candidate passport details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        // Check candidate data is available for CV executing or Not

        $updCvexectue = Candidate::find($post->id);

        // Change status and remove files of Company CV related to Candidate
        $compcvexs = Companycvexecute::where('cand_id','=',$post->id)->get();



        if(
            $post->cand_name != '' &&
            $post->photo_file != '' &&
            $post->exp_sal != '' &&
            $post->expwp_id != '' &&
            $post->marital_status != '' &&
            $post->religion_id != '' &&
            $post->dob != '' &&
            // $post->plb_id != '' &&
            $post->plb_text != '' &&
            $post->nation_id != '' &&
            $post->region_id != '' &&
            $post->pass_no != '' &&
            $post->pass_type != '' &&
            $post->doi != '' &&
            $post->doe != '' &&
            $post->poi != '' &&
            // $post->poi_text != '' &&
            $post->pass_file != '' &&
            $post->lic_file != '' &&
            $post->gulfexperience != '' &&
            $post->jobtype_id != '' &&
            $post->mobile_no != '' &&
            $post->contact_no != '' &&
            $post->relative_contact_no != '' &&
            $post->email != ''
        ){
            // update cv execute
            $updCvexectue->cv_execute = true;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }

            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

            // Update Publish Status
            $updCvexectue->publish = true;

        }else{
            // update cv execute
            $updCvexectue->cv_execute = false;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }
            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

            // Update Publish Status
            $updCvexectue->publish = false;

        }
        $updCvexectue->save();

        // return redirect()->back()->with('success','Candidate passport detail updated!');
        return response()->json(['success' => 'Candidate passport detail updated!']);
    }

    public function experienceupdate(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();
        $basepathstatus = Basepathstatus::first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }

        // dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }



        $post->experience = implode(',',$request->experience);
        $post->expcountry_id = implode(',',$request->expcountry_id);
        if($request->expcity_id != ''){
            $post->expcity_id = implode(',',$request->expcity_id);
        }

        // if($request->expcity_id_text != ''){
        //     $post->expcity_id_text = implode(",",$request->expcity_id_text);
        // }
        $post->proff_id = implode(',',$request->proff_id);
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        $post->overall_exp = array_sum($request->experience);
        $post->google_map = $request->google_map;
        $post->reference_no = $refNo;
        $post->gulfexperience = $request->gulfexperience;
        if($request->carknown_id != ''){
            $post->carknown_id = implode(",",$request->carknown_id);
        }else{
            $post->carknown_id = "";
        }

        if($request->vehical_transmission != ''){
            $post->vehical_transmission = implode(",",$request->vehical_transmission);
        }else{
            $post->vehical_transmission = "";
        }

        $post->save();

        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate experience detail updated!";
        $timeline->bodyMessage = "Candidate experience details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        // Check candidate data is available for CV executing or Not

        $updCvexectue = Candidate::find($post->id);

        // Change status and remove files of Company CV related to Candidate
        $compcvexs = Companycvexecute::where('cand_id','=',$post->id)->get();

        if(
            $post->cand_name != '' &&
            $post->photo_file != '' &&
            $post->exp_sal != '' &&
            $post->expwp_id != '' &&
            $post->marital_status != '' &&
            $post->religion_id != '' &&
            $post->dob != '' &&
            // $post->plb_id != '' &&
            $post->plb_text != '' &&
            $post->nation_id != '' &&
            $post->region_id != '' &&
            $post->pass_no != '' &&
            $post->pass_type != '' &&
            $post->doi != '' &&
            $post->doe != '' &&
            $post->poi != '' &&
            // $post->poi_text != '' &&
            $post->pass_file != '' &&
            $post->lic_file != '' &&
            $post->gulfexperience != '' &&
            $post->jobtype_id != '' &&
            $post->mobile_no != '' &&
            $post->contact_no != '' &&
            $post->relative_contact_no != '' &&
            $post->email != ''
        ){
            // update cv execute
            $updCvexectue->cv_execute = true;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_admin/admin/assets/images/pdf/'.$post->cv_execute_file);
            }

            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

            // Update Publish Status
            $updCvexectue->publish = true;

        }else{
            // update cv execute
            $updCvexectue->cv_execute = false;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_admin/admin/assets/images/pdf/'.$post->cv_execute_file);
            }
            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

            // Update Publish Status
            $updCvexectue->publish = false;
        }
        $updCvexectue->save();
        // return redirect()->back()->with('success','Candidate experience detail updated!');
        return response()->json(['success' => 'Candidate experience detail updated!']);
    }

   // Fit Medical Examine Result
   public function medicalFitupdate(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }

        // dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }


        $post->medical_examine_date = $request->medical_examine_date;
        $post->medical_expiry_date = $request->medical_expiry_date;
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        $post->reference_no = $refNo;
        $post->save();

        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate Fitmedical updated!";
        $timeline->bodyMessage = "Candidate Fitmedical details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();
        return redirect()->back()->with('success','Candidate Fitmedical detail updated!');

    }
    // Fitmedical endFunction
    // Unfit Medical Function
     public function unfitmedical(Request $request)
     {

        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }

         //dd($refNo);
        // $medical_examine_date='';
        // $medical_expiry_date='';
        // $on_medical_date='';
        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }
        // $post->medical_examine_date = $request->$medical_examine_date;
        // $post->medical_expiry_date = $request->$medical_expiry_date;
        // $post->$on_medical_date = $request->$on_medical_date;
        $post->medical_examine_unfit = $request->medical_examine_unfit;
        $post->medical_examine_unfit_date = $request->medical_examine_unfit_date;
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        $post->reference_no = $refNo;
        $post->save();

        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate Unfitmedical updated!";
        $timeline->bodyMessage = "Candidate Unfitmedical details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();
        return redirect()->back()->with('success','Candidate Unfitmedical detail updated!');
     }
    // UnfitMedical Function

    //Remedical Function Start
    public function remedical(Request $request)
    {

        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }

         //dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }
        $post->repeat_examine_date = $request->repeat_examine_date;
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        $post->reference_no = $refNo;
        $post->save();
        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();
        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate Repeat updated!";
        $timeline->bodyMessage = "Candidate Repeat details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();
        return redirect()->back()->with('success','Candidate Repeat detail updated!');
     }
    //Remedical Function End

    // Update On-Medical Start Function
    public function onmedical(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }

         //dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }
        $post->on_medical_date = $request->on_medical_date;
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        $post->reference_no = $refNo;
        $post->save();
        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();
        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate On Medical updated!";
        $timeline->bodyMessage = "Candidate On Medical details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();
        return redirect()->back()->with('success','Candidate On Medical detail updated!');
     }
    // Update On-Medical End Function

    // Update Waiting For Fitnes Function Start
    public function waitingforfitnes(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }

         //dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }
        $post->on_medical_date = $request->on_medical_date;
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        $post->reference_no = $refNo;
        $post->save();
        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();
        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate On Medical updated!";
        $timeline->bodyMessage = "Candidate On Medical details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();
        return redirect()->back()->with('success','Candidate On Medical detail updated!');
     }
    // Update Waiting For Fitnes function End
    public function medicalupdate(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);
        $chkPubst = Candpubst::where('cand_id','=',$id)->first();

        // check reference number is alloted or not
        if($post->reference_no != ''){
            // $refNo = $post->reference_no;
            if(str_contains($post->reference_no,'RF')){
                $refNo = $post->reference_no;
            }else{
                $refNo = 'RF'.$post->reference_no;
            }
        }else{
            // Check Candidate backup reference
            $chkcandref = Candidatebackupreference::count();

            if($chkcandref > 0){
                $refnumbc = Candidatebackupreference::orderBy('reference_no','ASC')->first();
                // $refNo = $refnumbc->reference_no;
                if(str_contains($refnumbc->reference_no,'RF')){
                    $refNo = $refnumbc->reference_no;
                }else{
                    $refNo = 'RF'.$refnumbc->reference_no;
                }
            }else{
                $countPostref = Candidate::where('reference_no','!=','')->count();
                $refNo = 'RF'.$countPostref + 1;
            }

        }

        // dd($refNo);

        if (!isset($chkPubst)) {
            $postpubst = new Candpubst();
            $postpubst->cand_id = $id;
            $postpubst->stage_name = 'Passport Stage';
            $postpubst->admin_id = Auth::guard('admin')->user()->id;
            $postpubst->save();
        }

        // Update status as per update
        if($request->medical_health_status == 'Fit'){
            $post->medical_examine_date = $request->medical_examine_date;
            $post->medical_expiry_date = $request->medical_expiry_date;
        }elseif($request->medical_health_status == 'Repeat'){
            $post->repeat_examine_date = $request->repeat_examine_date;
            $post->medical_expiry_date = $request->medical_expiry_date;
        }else{
            $post->medical_examine_date = Null;
            $post->repeat_examine_date = Null;
            $post->medical_expiry_date = Null;
        }


        // $post->medical_examine_unfit = $request->medical_examine_unfit;
        // $post->medical_examine_unfit_date = $request->medical_examine_unfit_date;
        // $post->repeat_examine_date = $request->repeat_examine_date;
        // $post->on_medical_date = $request->on_medical_date;
        // $post->waiting_medical = $request->waiting_medical;
        $post->medical_health_status = $request->medical_health_status;
        $post->age = (date('Y') - date('Y',strtotime($post->dob)));
        $post->reference_no = $refNo;
        $post->save();

        // Delete backup reference number
        $delbref = Candidatebackupreference::where('reference_no','=',$post->reference_no)->delete();

        // Update Candidate Medical History
        $candMediUpdate = new Candmedicalhistory();
        $candMediUpdate->cand_id = $id;
        $candMediUpdate->medical_status = $request->medical_health_status;
        if($request->medical_health_status == 'Fit'){
            $candMediUpdate->medical_examination_date = $request->medical_examine_date;
            $candMediUpdate->medical_expiry_date = $request->medical_expiry_date;

            $diff_date = strtotime($request->medical_expiry_date) - strtotime($request->medical_examine_date);
            $getDays = round($diff_date / (60 * 60 * 24));
            $days = $getDays.' Days';

            $candMediUpdate->medical_expire_in = $days;
        }
        if($request->medical_health_status == 'Repeat'){
            $candMediUpdate->remedical_date = $request->repeat_examine_date;
            $candMediUpdate->medical_expiry_date = $request->medical_expiry_date;

            $diff_date = strtotime($request->medical_expiry_date) - strtotime($request->repeat_examine_date);
            $getDays = round($diff_date / (60 * 60 * 24));
            $days = $getDays.' Days';

            $candMediUpdate->medical_expire_in = $days;
        }

        $candMediUpdate->last_update_date = date('Y-m-d');
        $candMediUpdate->notes = $request->notes;
        $candMediUpdate->staff_id = Auth::guard('admin')->user()->id;
        $candMediUpdate->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate medical detail updated!";
        $timeline->bodyMessage = "Candidate medical details updated by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();
        // return redirect()->back()->with('success','Candidate medical detail updated!');
        return response()->json(['success' => 'Candidate medical detail updated!']);
    }


    public function checkpassnoNew(Request $request)
    {
        $post = Candidate::where('pass_no','=',$request->pass_no)->count();

        if($post){
            return response()->json('1');
        }else{

        }
    }


    public function checkpassno(Request $request)
    {
        $post = Candidate::where('pass_no','=',$request->pass_no)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkmobno(Request $request)
    {
        $post = Candidate::where('mobile_no','=',$request->mobile_no)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckpassno(Request $request)
    {
        $post = Candidate::where('pass_no','=',$request->pass_no)->where('id','!=',$request->editID)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckmobno(Request $request)
    {
        $post = Candidate::where('mobile_no','=',$request->mobile_no)->where('id','!=',$request->editID)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function uploadPhoto(Request $request,$id)
    {
        $basepathstatus = Basepathstatus::first();
        $post = Candidate::find($id);

        $file = $request->file('file_photo');
        $name = $file->getClientOriginalName();
        // remove space from image
        $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
        $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
        $repspfilename = str_replace(" ","_",$filename_ren1);
        $new_file1 = $repspfilename.'.'.$fileext_ren1;
        if($basepathstatus->base_path_status == 1){
	        $file->move(base_path().'/public/admin/assets/images/candidate',$new_file1);
        }else{
            $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file1);
        }

        $photo_file = $new_file1;



        $post = Candidate::find($id);
        $post->photo_file = $photo_file;
        $post->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate profile photo uploaded!";
        $timeline->bodyMessage = "Candidate profile photo uploaded by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-files";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        return redirect()->back()->with('success','Candidate profile photo uploaded!');

    }

    public function uploadDocs(Request $request,$id)
    {
        $post = Candidate::find($id);
        $basepathstatus = Basepathstatus::first();

        $datetime = date("d-m-Y h:i");
        $repDate = str_replace(":","-",$datetime);
        $finalDateTime = str_replace(" ","-",$repDate);
        // passport file
        if ($request->hasFile('pass_file')) {

            $file = $request->file('pass_file');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
            // $repspfilename = str_replace(" ","_",$filename_ren1);
            $repspfilename = $post->pass_no."-PPFRONT-".$finalDateTime;
            $new_file1 = $repspfilename.'.'.$fileext_ren1;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file1);
            }

            $pass_file = $new_file1;
        } else {
            $pass_file = $post->pass_file;
        }

        // Passport Back File

        if ($request->hasFile('pass_back_file')) {

            $file = $request->file('pass_back_file');
            $name125 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren125 = pathinfo($name125,PATHINFO_FILENAME);
            $fileext_ren125 = pathinfo($name125,PATHINFO_EXTENSION);
            // $repspfilename = str_replace(" ","_",$filename_ren125);
            $repspfilename125 = $post->pass_no."-PPBACK-".$finalDateTime;
            $new_file125 = $repspfilename125.'.'.$fileext_ren125;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file125);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file125);
            }

            $pass_file125 = $new_file125;
        } else {
            $pass_file125 = $post->pass_back_file;
        }

        // license file
        if ($request->hasFile('lic_file')) {

            $file = $request->file('lic_file');
            $name2 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren2 = pathinfo($name2,PATHINFO_FILENAME);
            $fileext_ren2 = pathinfo($name2,PATHINFO_EXTENSION);
            // $repspfilename2 = str_replace(" ","_",$filename_ren2);
            $repspfilename2 = $post->pass_no."-DL-".$finalDateTime;
            $new_file2 = $repspfilename2.'.'.$fileext_ren2;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file2);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file2);
            }

            $lic_file = $new_file2;
        } else {
            $lic_file = $post->lic_file;
        }
        // CV file
        if ($request->hasFile('cv_file')) {

            $file = $request->file('cv_file');
            $name3 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren3 = pathinfo($name3,PATHINFO_FILENAME);
            $fileext_ren3 = pathinfo($name3,PATHINFO_EXTENSION);
            // $repspfilename3 = str_replace(" ","_",$filename_ren3);
            $repspfilename3 = $post->pass_no."-FP-".$finalDateTime;
            $new_file3 = $repspfilename3.'.'.$fileext_ren3;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file3);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file3);
            }

            $cv_file = $new_file3;
        } else {
            $cv_file = $post->cv_file;
        }
        // Photo file
        if ($request->hasFile('photo_file')) {

            $file = $request->file('photo_file');
            $name4 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren4 = pathinfo($name4,PATHINFO_FILENAME);
            $fileext_ren4 = pathinfo($name4,PATHINFO_EXTENSION);
            // $repspfilename4 = str_replace(" ","_",$filename_ren4);
            $repspfilename4 = $post->pass_no."-P-".$finalDateTime;
            $new_file4 = $repspfilename4.'.'.$fileext_ren4;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file4);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file4);
            }

            $photo_file = $new_file4;
        } else {
            $photo_file = $post->photo_file;
        }

        $post->pass_file = $pass_file;
        $post->lic_file = $lic_file;
        $post->cv_file = $cv_file;
        $post->photo_file = $photo_file;
        $post->pass_back_file = $pass_file125;
        $post->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate documents uploaded!";
        $timeline->bodyMessage = "Candidate documents uploaded by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-files";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        // Check candidate data is available for CV executing or Not

        $updCvexectue = Candidate::find($post->id);

        // Change status and remove files of Company CV related to Candidate
        $compcvexs = Companycvexecute::where('cand_id','=',$post->id)->get();


        if(
            $post->cand_name != '' &&
            $post->photo_file != '' &&
            $post->exp_sal != '' &&
            $post->expwp_id != '' &&
            $post->marital_status != '' &&
            $post->religion_id != '' &&
            $post->dob != '' &&
            // $post->plb_id != '' &&
            $post->plb_text != '' &&
            $post->nation_id != '' &&
            $post->region_id != '' &&
            $post->pass_no != '' &&
            $post->pass_type != '' &&
            $post->doi != '' &&
            $post->doe != '' &&
            $post->poi != '' &&
            // $post->poi_text != '' &&
            $post->pass_file != '' &&
            $post->lic_file != '' &&
            $post->gulfexperience != '' &&
            $post->jobtype_id != '' &&
            $post->mobile_no != '' &&
            $post->contact_no != '' &&
            $post->relative_contact_no != '' &&
            $post->email
        ){
            // update cv execute
            $updCvexectue->cv_execute = true;
            // remove filename from db and folder

            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }

            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

        }else{
            // update cv execute
            $updCvexectue->cv_execute = false;
            // remove filename from db and folder
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            }else{
                $filepath = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            }
            if(file_exists($filepath)){
                // unlink($filepath);
                File::delete($filepath);
                $updCvexectue->cv_execute_file = '';
            }else{
                $updCvexectue->cv_execute_file = '';
            }

            if(isset($compcvexs)){
                foreach($compcvexs as $compcvex){
                    // remove filename from db and folder
                    if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                        $filepathcv = base_path('public/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }else{
                        $filepathcv = base_path('public_html/admin/assets/images/pdf/partner/'.$compcvex->cv_file);
                    }

                    if(file_exists($filepathcv)){
                        // unlink($filepathcv);
                        File::delete($filepathcv);
                        $compcvex->cv_file = '';
                    }else{
                        $compcvex->cv_file = '';
                    }
                    $compcvex->status = false;
                    $compcvex->save();
                }
            }

             // update cv execute
             $updCvexectue->cv_execute = false;
        }
        $updCvexectue->save();

        // return redirect()->back()->with('success','Candidate document uploaded!');
        return response()->json(['success' => 'Candidate document uploaded!']);
    }

    public function uploadDocs2(Request $request,$id)
    {
        $basepathstatus = Basepathstatus::first();

        $candDet = Candidate::find($id);

        $datetime = date("d-m-Y h:i");
        $repDate = str_replace(":","-",$datetime);
        $finalDateTime = str_replace(" ","-",$repDate);

        if($request->label != ''){
            $labelName = str_replace(" ","-",$request->label);
            $fileName = $candDet->pass_no.'-'.$labelName.'-'.$finalDateTime;
        }else{
            $fileName = $candDet->pass_no.'-'.$finalDateTime;
        }



        // passport file
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
            // $repspfilename = str_replace(" ","_",$filename_ren1);
            // $repspfilename = str_replace(" ","_",$filename_ren1);
            $new_file1 = $fileName.'.'.$fileext_ren1;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file1);
            }

            $pass_file = $new_file1;
        } else {
            $pass_file = '';
        }


        // Upload Photo
        $post = new Candidatefile();
        $post->cand_id = $id;
        $post->label = $request->label;
        $post->filename = $pass_file;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate documents uploaded!";
        $timeline->bodyMessage = "Candidate documents uploaded by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-files";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        // return redirect()->back()->with('success','Candidate document uploaded!');
        return response()->json(['success' => 'Candidate document uploaded!']);
    }

    public function publishstg(Request $request,$id)
    {
        $post = Candidate::find($id);
        $chekpub = Candpubst::where('cand_id','=',$id)->first();
        $poiss = Placeofissue::orderBy('id','DESC')->get();
        $poi = Placeofissue::find($post->poi);

        $jobtypes = Profession::orderBy('id','DESC')->get();
        $countries = Country::orderBy('id','DESC')->get();
        $cities = City::orderBy('id','DESC')->get();

        $expconts = Country::wherein('id',explode(',',$post->expcountry_id))->get();
        $expcities = Expecworkcity::wherein('id',explode(',',$post->expcity_id))->get();
        $proffs = Profession::wherein('id',explode(',',$post->proff_id))->get();

        $expworklocs = Expecworkcity::orderBy('name')->get();

        $expcworloc = Expecworkcity::wherein('id',explode(",",$post->expwp_id))->get();

        $cars = Carnknown::orderBy('name','ASC')->where('status','=',1)->get();
        $carkns = Carnknown::wherein('carknown_id',explode(",",$post->carknown_id));

        $mycont = [];
        $myproff = [];
        $mycity = [];
        $myCarkn = [];

        foreach ($expconts as $expcont) {
            $mycont[] =  $expcont->name;
        }
        foreach ($expcities as $expcity) {
            $mycity[] =  $expcity->name;
        }
        foreach ($proffs as $proff) {
            $myproff[] =  $proff->eng_name;
        }

        foreach($carkns as $carkn){
            $myCarkn[] = $carkn->name;
        }

        if(!isset($chekpub)){
            $chkPubst = new Candpubst();
            $chkPubst->cand_id = $id;
            $chkPubst->stage_name = 'Passport Stage';
            $chkPubst->admin_id = Auth::guard('admin')->user()->id;
            $chkPubst->save();
        }

        if ($chekpub->passport_st == 0 && $chekpub->skill_exp_st == 0 && $chekpub->document_st == 0 && $chekpub->publish == 0) {
            return view('admin.candidate.passportStg',compact('post','cars','chekpub','poiss','poi'));
        }elseif($chekpub->passport_st == 1 && $chekpub->skill_exp_st == 0 && $chekpub->document_st == 0 && $chekpub->publish == 0){
            return view('admin.candidate.skillexpStg',compact('post','myCarkn','cars','chekpub','poiss','poi','jobtypes','countries','cities','mycont','mycity','myproff','expworklocs'));
        }elseif ($chekpub->passport_st == 1 && $chekpub->skill_exp_st == 1 && $chekpub->document_st == 0 && $chekpub->publish == 0) {
            return view('admin.candidate.docsStg',compact('post','cars','chekpub','poiss','poi'));
        }elseif($chekpub->passport_st == 1 && $chekpub->skill_exp_st == 1 && $chekpub->document_st == 1 && $chekpub->publish == 0){
            return view('admin.candidate.readyforpublish',compact('post','poiss','poi'));
        }else{
            return view('admin.candidate.publish',compact('post','chekpub','poiss','poi'));
        }

    }

    public function publishstgpass(Request $request)
    {
        $id = $request->editID;

        // Update in Candidate
        $post = Candidate::find($id);
        $post->cand_name = $request->cand_name;
        $post->pass_no = $request->pass_no;
        $post->pass_type = $request->pass_type;
        $post->dob = date('Y-m-d',strtotime($request->dob));
        $post->doi = date('Y-m-d',strtotime($request->doi));
        $post->doe = date('Y-m-d',strtotime($request->doe));
        $post->poi = $request->poi;
        // $post->poi_text = $request->poi_text;
        $post->save();

        // Update in Candidate Stage
        $postStg = Candpubst::where('cand_id','=',$id)->first();
        $postStg->passport_st = 1;
        $postStg->stage_name = 'Experience Stage';
        $postStg->save();


        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate passport verification stage!";
        $timeline->bodyMessage = "Candidate passport verified by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-zoom-check";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();


        return redirect()->route('admin.candidate.publish',$id);
    }

    public function publishskillandexp(Request $request)
    {
        $id = $request->editID;

        // Update in Candidate

        $post = Candidate::find($id);
        $post->experience = implode(',',$request->experience);
        $post->expcountry_id = implode(',',$request->expcountry_id);
        $post->expcity_id = implode(',',$request->expcity_id);
        $post->proff_id = implode(',',$request->proff_id);
        $post->lang_known = implode(',',$request->lang_known);
        $post->job_type = $request->job_type;
        $post->google_map = $request->google_map;

        $post->gulfexperience = $request->gulfexperience;
        if($request->carknown_id != ''){
            $post->carknown_id = implode(",",$request->carknown_id);
        }else{
            $post->carknown_id = "";
        }

        $post->save();

        // Update in Candidate Publish Status

        $postStg = Candpubst::where('cand_id','=',$id)->first();
        $postStg->skill_exp_st = 1;
        $postStg->stage_name = 'Document Stage';
        $postStg->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate skills and experience verified!";
        $timeline->bodyMessage = "Candidate skills and experience verified by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        return redirect()->route('admin.candidate.publish',$id);

    }

    public function publishDocs(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);

        $basepathstatus = Basepathstatus::first();

        // passport file
        if ($request->hasFile('pass_file')) {
            $file = $request->file('pass_file');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
            // $repspfilename = str_replace(" ","_",$filename_ren1);
            $repspfilename = "PP-".$post->pass_no."-".time();
            $new_file1 = $repspfilename.'.'.$fileext_ren1;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file1);
            }

            $pass_file = $new_file1;
        } else {
            $pass_file = $post->pass_file;
        }

        // license file
        if ($request->hasFile('lic_file')) {
            $file = $request->file('lic_file');
            $name2 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren2 = pathinfo($name2,PATHINFO_FILENAME);
            $fileext_ren2 = pathinfo($name2,PATHINFO_EXTENSION);
            // $repspfilename2 = str_replace(" ","_",$filename_ren2);
            $repspfilename2 = "DL-".$post->pass_no."-".time();
            $new_file2 = $repspfilename2.'.'.$fileext_ren2;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file2);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file2);
            }
            $lic_file = $new_file2;
        } else {
            $lic_file = $post->lic_file;
        }
        // CV file
        if ($request->hasFile('cv_file')) {
            $file = $request->file('cv_file');
            $name3 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren2 = pathinfo($name3,PATHINFO_FILENAME);
            $fileext_ren2 = pathinfo($name3,PATHINFO_EXTENSION);
            // $repspfilename3 = str_replace(" ","_",$filename_ren2);
            $repspfilename3 = "FP-".$post->pass_no."-".time();
            $new_file3 = $repspfilename3.'.'.$fileext_ren2;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file3);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file3);
            }
            $cv_file = $new_file3;
        } else {
            $cv_file = $post->cv_file;
        }
        // Photo file
        if ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $name4 = $file->getClientOriginalName();
            // remove space from image
            $filename_ren4 = pathinfo($name4,PATHINFO_FILENAME);
            $fileext_ren4 = pathinfo($name4,PATHINFO_EXTENSION);
            // $repspfilename4 = str_replace(" ","_",$filename_ren4);
            $repspfilename4 = "P-".$post->pass_no."-".time();
            $new_file4 = $repspfilename4.'.'.$fileext_ren4;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/candidate',$new_file4);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/candidate',$new_file4);
            }

            $photo_file = $new_file4;
        } else {
            $photo_file = $post->photo_file;
        }

        $post->pass_file = $pass_file;
        $post->lic_file = $lic_file;
        $post->cv_file = $cv_file;
        $post->photo_file = $photo_file;

        $post->save();

        // Update Candidate Stage

        if($post->pass_file != '' && $post->photo_file != ''){
            $postStg = Candpubst::where('cand_id','=',$id)->first();
            $postStg->document_st = 1;
            $postStg->stage_name = 'Ready For Publish';
            $postStg->save();
        }

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate documents verified!";
        $timeline->bodyMessage = "Candidate documents verified by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-files";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        return redirect()->route('admin.candidate.publish',$id);

    }

    public function uppublish(Request $request)
    {
        $id = $request->editID;
        $post = Candidate::find($id);

        // Update in Candidate
        if($request->publish == 1){
            $post->publish = 1;
        }else{
            $post->publish = 0;
        }
        $post->save();

        // Update in Candidate Stage
        $candPub = Candpubst::where('cand_id','=',$id)->first();
        if($request->publish == 1){
            $candPub->publish = 1;
            $candPub->stage_name = "Published";
            $candPub->save();

            // Create Timeline
            $timeline = new Activity();
            $timeline->cand_id = $id;
            $timeline->headline = "Candidate publised!";
            $timeline->bodyMessage = "Candidate publised by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-user-check";
            $timeline->admin_id = Auth::guard('admin')->user()->id;
            $timeline->save();

        }else{
            $candPub->publish = 0;
            $candPub->stage_name = "Unpublished";
            $candPub->save();

            // Create Timeline
            $timeline = new Activity();
            $timeline->cand_id = $id;
            $timeline->headline = "Candidate unpublished!";
            $timeline->bodyMessage = "Candidate unpublished by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
            $timeline->icons = "ti ti-user-x";
            $timeline->admin_id = Auth::guard('admin')->user()->id;
            $timeline->save();

        }




        return redirect()->route('admin.candidate.publish',$id);

    }

    public function backskillandexp($id)
    {
        $post = Candpubst::where('cand_id','=',$id)->first();
        $post->passport_st = 0;
        $post->stage_name = "Passport Stage";
        $post->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate back to Passport verifiaction Stage!";
        $timeline->bodyMessage = "Candidate back to Passport verifiaction Stage by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-chevrons-left";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        return redirect()->route('admin.candidate.publish',$id);
    }

    public function backdocsstg($id)
    {
        $post = Candpubst::where('cand_id','=',$id)->first();
        $post->skill_exp_st = 0;
        $post->stage_name = "Experience Stage";
        $post->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate back to Experience Stage!";
        $timeline->bodyMessage = "Candidate back to Experience Stage by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-chevrons-left";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        return redirect()->route('admin.candidate.publish',$id);
    }

    public function backpublish($id)
    {
        $post = Candpubst::where('cand_id','=',$id)->first();
        $post->document_st = 0;
        $post->publish = 0;
        $post->stage_name = "Document Stage";
        $post->save();

        // Change the status of Candidate Publish
        $candPost = Candidate::find($id);
        $candPost->publish = 0;
        $candPost->save();

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $id;
        $timeline->headline = "Candidate back to Document Stage!";
        $timeline->bodyMessage = "Candidate back to Document Stage by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-chevrons-left";
        $timeline->admin_id = Auth::guard('admin')->user()->id;
        $timeline->save();

        return redirect()->route('admin.candidate.publish',$id);
    }

    public function updateFilterList(Request $request){
        $post = Candidatefilterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->count();
        if($post > 0){
            $updateF = Candidatefilterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

            if($request->pass_typef == 1){
				$updateF->pass_type_filter = true;
			}else{
				$updateF->pass_type_filter = false;
			}

            if($request->job_typef == 1){
				$updateF->job_type_filter = true;
			}else{
				$updateF->job_type_filter = false;
			}

            if($request->create_byf == 1){
				$updateF->create_by_filter = true;
			}else{
				$updateF->create_by_filter = false;
			}

            if($request->careofff == 1){
				$updateF->careoff_filter = true;
			}else{
				$updateF->careoff_filter = false;
			}

            if($request->create_datef == 1){
				$updateF->create_date_filter = true;
			}else{
				$updateF->create_date_filter = false;
			}

            if($request->religionf == 1){
				$updateF->religion_filter = true;
			}else{
				$updateF->religion_filter = false;
			}

            if($request->regionf == 1){
				$updateF->region_filter = true;
			}else{
				$updateF->region_filter = false;
			}

            if($request->cityf == 1){
				$updateF->city_filter = true;
			}else{
				$updateF->city_filter = false;
			}

            if($request->experience_regionf == 1){
				$updateF->experience_region_filter = true;
			}else{
				$updateF->experience_region_filter = false;
			}

            if($request->medical_expiry_datef == 1){
				$updateF->medical_expiry_date_filter = true;
			}else{
				$updateF->medical_expiry_date_filter = false;
			}

            if($request->candidate_statusf == 1){
				$updateF->candidate_status_filter = true;
			}else{
				$updateF->candidate_status_filter = false;
			}

            if($request->publish_statusf == 1){
				$updateF->publish_status_filter = true;
			}else{
				$updateF->publish_status_filter = false;
			}

            if($request->new_candidate_statusf == 1){
				$updateF->new_candidate_status_filter = true;
			}else{
				$updateF->new_candidate_status_filter = false;
			}

            if($request->ready_for_published_statusf == 1){
				$updateF->ready_for_published_status_filter = true;
			}else{
				$updateF->ready_for_published_status_filter = false;
			}

            if($request->cand_medical_statusf == 1){
				$updateF->medical_health_status_filter = true;
			}else{
				$updateF->medical_health_status_filter = false;
			}

            if($request->cand_payment_statusf == 1){
				$updateF->cand_payment_status_filter = true;
			}else{
				$updateF->cand_payment_status_filter = false;
			}

            if($request->sourcing_date_rangef == 1){
				$updateF->sourcing_date_filter = true;
			}else{
				$updateF->sourcing_date_filter = false;
			}


            $updateF->save();
    		return response()->json('success');
        }else{
            $newFilter = new Candidatefilterlist();

            $newFilter->admin_id = Auth::guard('admin')->user()->id;

            if($request->pass_typef == 1){
				$newFilter->pass_type_filter = true;
			}else{
				$newFilter->pass_type_filter = false;
			}

            if($request->job_typef == 1){
				$newFilter->job_type_filter = true;
			}else{
				$newFilter->job_type_filter = false;
			}

            if($request->create_byf == 1){
				$newFilter->create_by_filter = true;
			}else{
				$newFilter->create_by_filter = false;
			}

            if($request->careofff == 1){
				$newFilter->careoff_filter = true;
			}else{
				$newFilter->careoff_filter = false;
			}

            if($request->create_datef == 1){
				$newFilter->create_date_filter = true;
			}else{
				$newFilter->create_date_filter = false;
			}

            if($request->religionf == 1){
				$newFilter->religion_filter = true;
			}else{
				$newFilter->religion_filter = false;
			}

            if($request->regionf == 1){
				$newFilter->region_filter = true;
			}else{
				$newFilter->region_filter = false;
			}

            if($request->cityf == 1){
				$newFilter->city_filter = true;
			}else{
				$newFilter->city_filter = false;
			}

            if($request->experience_regionf == 1){
				$newFilter->experience_region_filter = true;
			}else{
				$newFilter->experience_region_filter = false;
			}

            if($request->medical_expiry_datef == 1){
				$newFilter->medical_expiry_date_filter = true;
			}else{
				$newFilter->medical_expiry_date_filter = false;
			}

            if($request->candidate_statusf == 1){
				$newFilter->candidate_status_filter = true;
			}else{
				$newFilter->candidate_status_filter = false;
			}

            if($request->publish_statusf == 1){
				$newFilter->publish_status_filter = true;
			}else{
				$newFilter->publish_status_filter = false;
			}

            if($request->new_candidate_statusf == 1){
				$newFilter->new_candidate_status_filter = true;
			}else{
				$newFilter->new_candidate_status_filter = false;
			}

            if($request->ready_for_published_statusf == 1){
				$newFilter->ready_for_published_status_filter = true;
			}else{
				$newFilter->ready_for_published_status_filter = false;
			}

            if($request->cand_medical_statusf == 1){
				$newFilter->medical_health_status_filter = true;
			}else{
				$newFilter->medical_health_status_filter = false;
			}

            if($request->cand_payment_statusf == 1){
				$newFilter->cand_payment_status_filter = true;
			}else{
				$newFilter->cand_payment_status_filter = false;
			}

            if($request->sourcing_date_rangef == 1){
				$newFilter->sourcing_date_filter = true;
			}else{
				$newFilter->sourcing_date_filter = false;
			}

            $newFilter->save();

            return response()->json('success');
        }
    }

    public function publishedSt(Request $request,$id){
        $post = Candidate::find($id);
        $post->publish = $request->publish;
        $post->save();

        // change published status
        $updateSt = Candpubst::where('cand_id','=',$id)->first();
        if($request->publish == 1){
            $updateSt->stage_name = 'Publish';
            if($updateSt->passport_st == 0){
                $updateSt->passport_st = true;
            }

            if($updateSt->skill_exp_st == 0){
                $updateSt->skill_exp_st = true;
            }

            if($updateSt->document_st == 0){
                $updateSt->document_st = true;
            }

        }else{
            $updateSt->stage_name = 'Unpublish';
        }

        $updateSt->publish = $request->publish;
        $updateSt->save();

        return redirect()->back()->with('success','Publish status updated!');
    }

    public function publishedbulkSt(Request $request){
        $idsv = explode(',', $request->idsv);
        foreach($idsv as $ids){
            $post = Candidate::find($ids);
            $post->publish = $request->publish;
            $post->save();

            // change published status
            $updateSt = Candpubst::where('cand_id','=',$ids)->first();
            if($request->publish == 1){
                $updateSt->stage_name = 'Publish';
                if($updateSt->passport_st == 0){
                    $updateSt->passport_st = true;
                }
                if($updateSt->skill_exp_st == 0){
                    $updateSt->skill_exp_st = true;
                }
                if($updateSt->document_st == 0){
                    $updateSt->document_st = true;
                }
            }else{
                $updateSt->stage_name = 'Unpublish';
            }
            $updateSt->publish = $request->publish;
            $updateSt->save();
        }
        return redirect()->back()->with('success','Publish status updated!');
    }

    public function checkdelcand(Request $request){
        $data1 = Bookingpayment::where('cand_id','=',$request->id)->count();

        if($data1 > 0){
            return response()->json('1');
        }else{

        }
    }

    public function candDel(Request $request){
        $id = $request->staff_id;
        $basepathstatus = Basepathstatus::first();

        $post = Candidate::find($id);
        $candFiles = Candidatefile::where('cand_id','=',$id)->get();
        $candMedHist = Candmedicalhistory::where('cand_id','=',$id)->get();
        $candpubst = Candpubst::where('cand_id','=',$id)->get();
        $candSc = Candsercharge::where('cand_id','=',$id)->get();
        $candStatus = CandStatus::where('cand_id','=',$id)->get();
        $candpayment = Paymentcand::where('cand_id','=',$id)->get();

        // Delete Images related to posts
        if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
            $filepath1 = base_path('public/admin/assets/images/candidate/'.$post->pass_file);
            $filepath2 = base_path('public/admin/assets/images/candidate/'.$post->pass_back_file);
            $filepath3 = base_path('public/admin/assets/images/candidate/'.$post->lic_file);
            $filepath4 = base_path('public/admin/assets/images/candidate/'.$post->cv_file);
            $filepath5 = base_path('public/admin/assets/images/candidate/'.$post->photo_file);
            $filepath6 = base_path('public/admin/assets/images/pdf/'.$post->cv_execute_file);
            $filepath7 = base_path('public/admin/assets/images/candidate/'.$post->musaned_file);

        }else{
            $filepath1 = base_path('public_html/admin/assets/images/candidate/'.$post->pass_file);
            $filepath2 = base_path('public_html/admin/assets/images/candidate/'.$post->pass_back_file);
            $filepath3 = base_path('public_html/admin/assets/images/candidate/'.$post->lic_file);
            $filepath4 = base_path('public_html/admin/assets/images/candidate/'.$post->cv_file);
            $filepath5 = base_path('public_html/admin/assets/images/candidate/'.$post->photo_file);
            $filepath6 = base_path('public_html/admin/assets/images/pdf/'.$post->cv_execute_file);
            $filepath7 = base_path('public_html/admin/assets/images/candidate/'.$post->musaned_file);

        }

        if(file_exists($filepath1)){
            File::delete($filepath1);
        }
        if(file_exists($filepath2)){
            File::delete($filepath2);
        }
        if(file_exists($filepath3)){
            File::delete($filepath3);
        }
        if(file_exists($filepath4)){
            File::delete($filepath4);
        }
        if(file_exists($filepath5)){
            File::delete($filepath5);
        }
        if(file_exists($filepath6)){
            File::delete($filepath6);
        }
        if(file_exists($filepath7)){
            File::delete($filepath7);
        }

        $post->delete();

        foreach($candMedHist as $candMedHis){
            $candMedHis->delete();
        }

        foreach($candStatus as $candStatu){
            $candStatu->delete();
        }

        foreach($candSc as $candS){
            $candS->delete();
        }
        foreach($candSc as $candS){
            $candS->delete();
        }

        foreach($candFiles as $candFile){
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath8 = base_path('public/admin/assets/images/candidate/'.$candFile->filename);

            }else{
                $filepath8 = base_path('public_html/admin/assets/images/candidate/'.$candFile->filename);

            }
            if(file_exists($filepath8)){
                File::delete($filepath8);
            }

            $candFile->delete();

        }

        foreach($candpayment as $candpayment){
            if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
                $filepath9 = base_path('public/admin/assets/images/candidate/'.$candpayment->payment_slip);

            }else{
                $filepath9 = base_path('public_html/admin/assets/images/candidate/'.$candpayment->payment_slip);

            }
            if(file_exists($filepath9)){
                File::delete($filepath9);
            }

            $candpayment->delete();

        }






        return redirect()->back()->with('success','Candidate deleted!');
    }

    public function paymentSlipView(Request $request){
        $post = Paymentcand::find($request->id);

        $imagePath = url('/admin/assets/images/payment/candidate/'.$post->payment_slip);

        return response()->json($imagePath);
    }

    public function candlimitupdt(Request $request,$id){
        $chk_cand_limit = Candidatebookinglimit::where('cand_id','=',$id)->get();

        if($chk_cand_limit->count() > 0){
            $chk_cand_limit->limit = $request->limit;
            $chk_cand_limit->save();

        }else{
            $post = new Candidatebookinglimit();
            $post->cand_id = $id;
            $post->limit = $request->limit;
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();
        }

        return redirect()->back()->with('success','Candidate limit updated!');
    }

    public function candstatusGet(Request $request){
        // $post = CandStatus::where('cand_id','=',$request->id)->first();
        $post = DB::table('candidates as cand')
            ->leftJoin('cand_statuses as candSt','cand.id','=','candSt.cand_id')
            ->select('candSt.*','cand.cand_name','cand.pass_no')
            ->where('cand.id','=',$request->id)
            ->first();
        return response()->json($post);
    }

    public function newcandidateupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->candidate_are_ready = $request->candidate_are_ready;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->candidate_are_ready == 1){
            $cand->candidate_current_status = "Candidate Are Ready";
            $timeline->headline = "Update Status Candidate Are Ready";
            $timeline->bodyMessage = "Update Status Candidate Are Ready by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "New Candidate";
            $timeline->headline = "Update Status New Candidate";
            $timeline->bodyMessage = "Update Status New Candidate by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }

        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function publishforselectionupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->published_for_selection = $request->published_for_selection;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->published_for_selection == 1){
            $cand->candidate_current_status = "Published For Selection";
            $cand->publish = true;
            $timeline->headline = "Update Status Published For Selection";
            $timeline->bodyMessage = "Update Status Published For Selection by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');

        }else{
            $cand->candidate_current_status = "Candidate Are Ready";
            $cand->publish = false;
            $timeline->headline = "Update Status Candidate Are Ready";
            $timeline->bodyMessage = "Update Status Candidate Are Ready by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');

        }
        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function selectedupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->selected = $request->selected;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->selected == 1){
            $cand->candidate_current_status = "Selected";
            $timeline->headline = "Update Status Selected";
            $timeline->bodyMessage = "Update Status Selected by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Published For Selection";
            $timeline->headline = "Update Status Published For Selection";
            $timeline->bodyMessage = "Update Status Published For Selection by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }

        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function visareceivedupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);

        $post->visa_received = $request->visa_received;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->visa_received == 1){
            $cand->candidate_current_status = "Visa Received";
            $cand->publish = false;
            $timeline->headline = "Update Status Visa Received";
            $timeline->bodyMessage = "Update Status Visa Received by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Selected";
            $cand->publish = true;
            $timeline->headline = "Update Status Selected";
            $timeline->bodyMessage = "Update Status Selected by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }

        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function passportinembassyupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->passport_in_embassy = $request->passport_in_embassy;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->passport_in_embassy == 1){
            $cand->candidate_current_status = "Passport In Embassy";
            $timeline->headline = "Update Status Passport In Embassy";
            $timeline->bodyMessage = "Update Status Passport In Embassy by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Visa Received";
            $timeline->headline = "Update Status Visa Received";
            $timeline->bodyMessage = "Update Status Visa Received by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }

        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function visastampedupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->visa_stamped = $request->visa_stamped;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->visa_stamped == 1){
            $cand->candidate_current_status = "Visa Stamped";
            $timeline->headline = "Update Status Visa Stamped";
            $timeline->bodyMessage = "Update Status Visa Stamped by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Passport In Embassy";
            $timeline->headline = "Update Status Passport In Embassy";
            $timeline->bodyMessage = "Update Status Passport In Embassy by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }
        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function appliedforemigrationupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->applied_for_emigration = $request->applied_for_emigration;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->applied_for_emigration == 1){
            $cand->candidate_current_status = "Applied For Emigration";
            $timeline->headline = "Update Status Applied For Emigration";
            $timeline->bodyMessage = "Update Status Applied For Emigration by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Visa Stamped";
            $timeline->headline = "Update Status Visa Stamped";
            $timeline->bodyMessage = "Update Status Visa Stamped by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }
        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function emigrationapprovedupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->emigration_approved = $request->emigration_approved;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->emigration_approved == 1){
            $cand->candidate_current_status = "Emigration Approved";
            $timeline->headline = "Update Status Emigration Approved";
            $timeline->bodyMessage = "Update Status Emigration Approved by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Applied For Emigration";
            $timeline->headline = "Update Status Applied For Emigration";
            $timeline->bodyMessage = "Update Status Applied For Emigration by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }
        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function waitingforticketupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->waiting_for_flight_ticket = $request->waiting_for_flight_ticket;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->waiting_for_flight_ticket == 1){
            $cand->candidate_current_status = "Waiting For Flight Ticket";
            $timeline->headline = "Update Status Waiting For Flight Ticket";
            $timeline->bodyMessage = "Update Status Waiting For Flight Ticket by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');

        }else{
            $cand->candidate_current_status = "Emigration Approved";
            $timeline->headline = "Update Status Emigration Approved";
            $timeline->bodyMessage = "Update Status Emigration Approved by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');

        }
        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function ticketconfirmedupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->ticket_confirmed = $request->ticket_confirmed;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->ticket_confirmed == 1){
            $cand->candidate_current_status = "Ticket Confirmed";
            $timeline->headline = "Update Status Ticket Confirmed";
            $timeline->bodyMessage = "Update Status Ticket Confirmed by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Waiting For Flight Ticket";
            $timeline->headline = "Update Status Waiting For Flight Ticket";
            $timeline->bodyMessage = "Update Status Waiting For Flight Ticket by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }
        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function deployedupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);
        $post->deployed = $request->deployed;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($request->deployed == 1){
            $cand->candidate_current_status = "Deployed";
            $timeline->headline = "Update Status Deployed";
            $timeline->bodyMessage = "Update Status Deployed by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Ticket Confirmed";
            $timeline->headline = "Update Status Ticket Confirmed";
            $timeline->bodyMessage = "Update Status Ticket Confirmed by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }

        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function cancelledupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);

        $post->cancelled = $request->cancelled;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table
        if($post->new_candidate == 1 && $post->candidate_are_ready == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "New Candidate";
            $timeline->headline = "Update Status New Candidate";
            $timeline->bodyMessage = "Update Status New Candidate by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->candidate_are_ready == 1 && $post->published_for_selection == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Candidate Are Ready";
            $timeline->headline = "Update Status Candidate Are Ready";
            $timeline->bodyMessage = "Update Status Candidate Are Ready by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->published_for_selection == 1 && $post->selected == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Published For Selection";
            $timeline->headline = "Update Status Published For Selection";
            $timeline->bodyMessage = "Update Status Published For Selection by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->selected == 1 && $post->visa_received == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Selected";
            $timeline->headline = "Update Status Selected";
            $timeline->bodyMessage = "Update Status Selected by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->visa_received == 1 && $post->passport_in_embassy == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Visa Received";
            $timeline->headline = "Update Status Visa Received";
            $timeline->bodyMessage = "Update Status Visa Received by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->passport_in_embassy == 1 && $post->visa_stamped == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Passport In Embassy";
            $timeline->headline = "Update Status Passport In Embassy";
            $timeline->bodyMessage = "Update Status Passport In Embassy by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->visa_stamped == 1 && $post->applied_for_emigration == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Visa Stamped";
            $timeline->headline = "Update Status Visa Stamped";
            $timeline->bodyMessage = "Update Status Visa Stamped by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->applied_for_emigration == 1 && $post->emigration_approved == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Applied For Emigration";
            $timeline->headline = "Update Status Applied For Emigration";
            $timeline->bodyMessage = "Update Status Applied For Emigration by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->emigration_approved == 1 && $post->waiting_for_flight_ticket == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Emigration Approved";
            $timeline->headline = "Update Status Emigration Approved";
            $timeline->bodyMessage = "Update Status Emigration Approved by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->waiting_for_flight_ticket == 1 && $post->ticket_confirmed == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Waiting For Flight Ticket";
            $timeline->headline = "Update Status Waiting For Flight Ticket";
            $timeline->bodyMessage = "Update Status Waiting For Flight Ticket by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->ticket_confirmed == 1 && $post->deployed == 0 && $request->cancelled == 0){
            $cand->candidate_current_status = "Ticket Confirmed";
            $timeline->headline = "Update Status Ticket Confirmed";
            $timeline->bodyMessage = "Update Status Ticket Confirmed by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->deployed == 1 && $request->cancelled == 0){
            $cand->candidate_current_status = "Deployed";
            $timeline->headline = "Update Status Deployed";
            $timeline->bodyMessage = "Update Status Deployed by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Cancelled";
            $timeline->headline = "Update Status Cancelled";
            $timeline->bodyMessage = "Update Status Cancelled by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }
        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function holdupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);

        $post->hold = $request->hold;
        $post->save();

        // Create Timeline for Activity
        $timeline = new Activity();
        $timeline->cand_id = $request->cand_id;
        $timeline->icons = "ti ti-user-plus";
        $timeline->admin_id = Auth::guard('admin')->user()->id;

        // Current Status on Candidate Table

        if($post->new_candidate == 1 && $post->candidate_are_ready == 0 && $request->hold == 0){
            $cand->candidate_current_status = "New Candidate";
            $timeline->headline = "Update Status New Candidate";
            $timeline->bodyMessage = "Update Status New Candidate by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->candidate_are_ready == 1 && $post->published_for_selection == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Candidate Are Ready";
            $timeline->headline = "Update Status Candidate Are Ready";
            $timeline->bodyMessage = "Update Status Candidate Are Ready by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->published_for_selection == 1 && $post->selected == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Published For Selection";
            $timeline->headline = "Update Status Published For Selection";
            $timeline->bodyMessage = "Update Status Published For Selection by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->selected == 1 && $post->visa_received == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Selected";
            $timeline->headline = "Update Status Selected";
            $timeline->bodyMessage = "Update Status Selected by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->visa_received == 1 && $post->passport_in_embassy == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Visa Received";
            $timeline->headline = "Update Status Visa Received";
            $timeline->bodyMessage = "Update Status Visa Received by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->passport_in_embassy == 1 && $post->visa_stamped == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Passport In Embassy";
            $timeline->headline = "Update Status Passport In Embassy";
            $timeline->bodyMessage = "Update Status Passport In Embassy by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->visa_stamped == 1 && $post->applied_for_emigration == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Visa Stamped";
            $timeline->headline = "Update Status Visa Stamped";
            $timeline->bodyMessage = "Update Status Visa Stamped by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->applied_for_emigration == 1 && $post->emigration_approved == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Applied For Emigration";
            $timeline->headline = "Update Status Applied For Emigration";
            $timeline->bodyMessage = "Update Status Applied For Emigration by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->emigration_approved == 1 && $post->waiting_for_flight_ticket == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Emigration Approved";
            $timeline->headline = "Update Status Emigration Approved";
            $timeline->bodyMessage = "Update Status Emigration Approved by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->waiting_for_flight_ticket == 1 && $post->ticket_confirmed == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Waiting For Flight Ticket";
            $timeline->headline = "Update Status Waiting For Flight Ticket";
            $timeline->bodyMessage = "Update Status Waiting For Flight Ticket by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->ticket_confirmed == 1 && $post->deployed == 0 && $request->hold == 0){
            $cand->candidate_current_status = "Ticket Confirmed";
            $timeline->headline = "Update Status Ticket Confirmed";
            $timeline->bodyMessage = "Update Status Ticket Confirmed by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }elseif($post->deployed == 1 && $request->hold == 0){
            $cand->candidate_current_status = "Deployed";
            $timeline->headline = "Update Status Deployed";
            $timeline->bodyMessage = "Update Status Deployed by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }else{
            $cand->candidate_current_status = "Hold";
            $timeline->headline = "Update Status Hold";
            $timeline->bodyMessage = "Update Status Hold by ".Auth::guard('admin')->user()->name.' on '.date('d-m-Y h:i A');
        }

        $cand->save();

        $timeline->save();

        return response()->json($post);
    }

    public function visacancelledupdate(Request $request){
        $post = CandStatus::where('cand_id',$request->cand_id)->first();
        $cand = Candidate::find($request->cand_id);

        $post->visa_cencelled = $request->visa_cencelled;
        $post->save();

        // Current Status on Candidate Table
        $cand->candidate_current_status = "Visa Cancelled";
        $cand->save();

        return response()->json($post);
    }

    public function candPhotoDel(Request $request, $id){
        $post = Candidate::find($id);
        $basepathstatus = Basepathstatus::first();
        // remove filename from db and folder
        if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
            $filepath = base_path('public/admin/assets/images/candidate/'.$post->photo_file);
        }else{
            $filepath = base_path('public_html/admin/assets/images/candidate/'.$post->photo_file);
        }

        if(file_exists($filepath)){
            File::delete($filepath);
            $post->photo_file = '';
        }else{
            $post->photo_file = '';
        }
        $post->save();

        // return redirect()->back()->with('success','Photo Deleted!');
        return response()->json(['success' => 'Photo Deleted!']);
    }

    public function candPassportBackDel(Request $request, $id){
        $post = Candidate::find($id);
        $basepathstatus = Basepathstatus::first();
        // remove filename from db and folder
        if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
            $filepath = base_path('public/admin/assets/images/candidate/'.$post->pass_back_file);
        }else{
            $filepath = base_path('public_html/admin/assets/images/candidate/'.$post->pass_back_file);
        }

        if(file_exists($filepath)){
            File::delete($filepath);
            $post->pass_back_file = '';
        }else{
            $post->pass_back_file = '';
        }
        $post->save();

        // return redirect()->back()->with('success','Passport Deleted!');
        return response()->json(['success' => 'Passport Deleted!']);
    }

    public function candLicenseDel(Request $request, $id){
        $post = Candidate::find($id);
        $basepathstatus = Basepathstatus::first();
        // remove filename from db and folder
        if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
            $filepath = base_path('public/admin/assets/images/candidate/'.$post->lic_file);
        }else{
            $filepath = base_path('public_html/admin/assets/images/candidate/'.$post->lic_file);
        }

        if(file_exists($filepath)){
            File::delete($filepath);
            $post->lic_file = '';
        }else{
            $post->lic_file = '';
        }
        $post->save();

        // return redirect()->back()->with('success','License Deleted!');
        return response()->json(['success' => 'License Deleted!']);
    }

    public function candfullimageDel(Request $request, $id){
        $post = Candidate::find($id);
        $basepathstatus = Basepathstatus::first();
        // remove filename from db and folder
        if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
            $filepath = base_path('public/admin/assets/images/candidate/'.$post->cv_file);
        }else{
            $filepath = base_path('public_html/admin/assets/images/candidate/'.$post->cv_file);
        }

        if(file_exists($filepath)){
            File::delete($filepath);
            $post->cv_file = '';
        }else{
            $post->cv_file = '';
        }
        $post->save();

        // return redirect()->back()->with('success','Full Image Deleted!');
        return response()->json(['success' => 'Full Image Deleted!']);
    }

    public function candmusanedDel(Request $request, $id){
        $post = Candidate::find($id);
        $basepathstatus = Basepathstatus::first();
        // remove filename from db and folder
        if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
            $filepath = base_path('public/admin/assets/images/candidate/'.$post->musaned_file);
        }else{
            $filepath = base_path('public_html/admin/assets/images/candidate/'.$post->musaned_file);
        }

        if(file_exists($filepath)){
            File::delete($filepath);
            $post->musaned_file = '';
        }else{
            $post->musaned_file = '';
        }
        $post->save();

        // return redirect()->back()->with('success','Musaned Deleted!');
        return response()->json(['success' => 'Musaned Deleted!']);
    }

    public function candfileDel(Request $request){
        $post = Candidatefile::find($request->filecand_id);

        $basepathstatus = Basepathstatus::first();
        // remove filename from db and folder
        if(isset($basepathstatus) && $basepathstatus->base_path_status == 1){
            $filepath = base_path('public/admin/assets/images/candidate/'.$post->filename);
        }else{
            $filepath = base_path('public_html/admin/assets/images/candidate/'.$post->filename);
        }

        if(file_exists($filepath)){
            File::delete($filepath);
            $post->delete();
        }else{
            $post->delete();
        }


        // return redirect()->back()->with('success','File Deleted!');
        return response()->json(['success' => 'File Deleted!']);
    }

    public function candidateTransactionList(){
        return view('admin.candidate.transaction.index');
    }

    public function candidateTransactionListJson(Request $request){

        $posts = DB::table('paymentcands as paymentcand')
            ->leftjoin('candidates as cand','cand.id','=','paymentcand.cand_id')
            ->leftjoin('admins as admin','admin.id','=','paymentcand.admin_id')
            ->select('paymentcand.*','cand.cand_name','cand.pass_no','cand.cand_payment_status','admin.name as uname')
            ->orderBy('paymentcand.id','DESC')
            ->get();

        $data['data'] = $posts;
        return response()->json($data);
    }

    public function getCVData(Request $request){
        $ids = explode(",",$request->ids);

        // Execute List

        $executedLists = DB::table('companycvexecutes as compcvex')
        ->leftjoin('candidates as cand','cand.id','compcvex.cand_id')
        ->select("cand.cand_name","cand.pass_no","compcvex.cand_id")
        ->wherein("compcvex.cand_id",$ids)
        ->where("compcvex.status",'=',1);
        if($request->pertner_id != ''){
           $execs =  $executedLists->where("compcvex.partner_id","=",$request->pertner_id);
        }
        $execs = $executedLists->get();

        $availableIDS = [];
        foreach($execs as $exec){
            $availableIDS [] = $exec->cand_id;
        }

        $notIDS = array_diff($ids,$availableIDS);

        // count($ids) == count($execs)
        $res = "";
        if(count($notIDS) == 0){
            $res .= "<p class='text-danger'>All CVS are Executed</p>";
            $data['cv_status'] = "1";
        }else{
            $notExecuteLists = Candidate::wherein('id',$notIDS)->get();
            $inotex = 1;

            $res .= "<table class='table table-bordered'><thead><tr><th>#</th><th>Candidate</th><th>Passport No</th><th>Action</th></tr></thead><tbody>";

            $res .= "<tr><td colspan='4' class='text-center'>Required CV List</td></tr>";
            foreach($notExecuteLists as $notExecuteList){
                $url = url("admin/candidate/view/".$notExecuteList->id);
                $res .= "<tr><td>".$inotex."</td><td>".$notExecuteList->cand_name."</td><td>".$notExecuteList->pass_no."</td><td><a href='".$url."' target='_blank'><i class='ti ti-edit ti-sm me-2'></i></a></td></tr>";

                $inotex++;
            }
            $res .= "</tbody></table>";
            $res .= "<div class='mt-2'><a href='#' class='reloadModal'><i class='ti ti-refresh ti-sm me-2'></i></a></div>";
            $data['cv_status'] = "0";
        }

        $data['res'] = $res;

        return response($data);



    }

    public function mofaupdate(Request $request){
        $post = Candidate::find($request->editID);

        $post->mofa_no = $request->mofa_no;
        $post->save();

        return response()->json(['success' => 'Mofa Updated!']);
    }

    public function flightpdate(Request $request){
        $post = Candidate::find($request->editID);
        $post->flight_date = $request->flight_date;
        $post->save();

        return response()->json(['success' => 'Flight Updated!']);
    }

}
