<?php

namespace App\Http\Controllers;

use App\Admin\Medicalcenter;
use App\Medicalcentercity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;
use Session;
use File;

class MedicalCenterController extends Controller
{
    public function index()
    {
        $medcities = Medicalcentercity::orderBy('name','ASC')->get();
        $pageConfigs = ['pageHeader' => false];
        return view('content.service.medical_center.index',['pageConfigs' => $pageConfigs,'medcities' => $medcities]);
    }

    public function indexjson(Request $request){
        $post = DB::table('medicalcenters as medcent')
            ->leftjoin('medicalcentercities as medciti','medciti.id','=','medcent.city_id')
            ->leftJoin('users as user','user.user_id','=','medcent.user_id')
            ->select('medcent.*','user.name as uname','medciti.name as citname')
            ->get();

        return DataTables::of($post)->toJson();
    }

    public function store(Request $request){
        $post = new Medicalcenter();
        $post->name = $request->name;
        $post->doctor_name = $request->doctor_name;
        $post->c_person = $request->c_person;
        $post->contact_type = $request->contact_type;
        $post->c_number = $request->c_number;
        $post->w_number = $request->w_number;
        $post->pri_email = $request->pri_email;
        $post->sec_email = $request->sec_email;
        // $post->city = $request->city;
        $post->city_id = $request->city_id;
        $post->state = $request->state;
        $post->address = $request->address;
        $post->notes = $request->notes;
        $post->med_cent_beh = $request->med_cent_beh;
        $post->web_url = $request->web_url;
        $post->user_id = Auth::user()->user_id;
        $post->save();

        Session::flash('success','Medical center created!');
        return redirect()->back();
    }

    public function view($id){
        $post = DB::table('medicalcenters as medcent')
            ->leftJoin('users as user','user.user_id','=','medcent.user_id')
            ->select('medcent.*','user.name as uname')
            ->where('medcent.id','=',$id)
            ->first();

        $pageConfigs = ['pageHeader' => false];
        return view('content.service.medical_center.view',['pageConfigs' => $pageConfigs,'post' => $post]);
    }

    public function setStatus(Request $request){
        $id = $request->id;
        $post = Medicalcenter::find($id);
        $post->status = $request->status;
        $post->save();
        Session::flash('success','Status changed!');
        return redirect()->back();
    }

    public function setCtype(Request $request){
        $id = $request->id;
        $post = Medicalcenter::find($id);
        $post->contact_type = $request->contact_type;
        $post->save();
        Session::flash('success','Contact type changed!');
        return redirect()->back();
    }

    public function editmc(Request $request){
        $id = $request->id;
        $post = Medicalcenter::find($id);
        return response()->json($post);
    }

    public function updateMC(Request $request){
        $id = $request->id;
        $post = Medicalcenter::find($id);

        $post->name = $request->name;
        $post->doctor_name = $request->doctor_name;
        $post->c_person = $request->c_person;
        $post->contact_type = $request->contact_type;
        $post->c_number = $request->c_number;
        $post->w_number = $request->w_number;
        $post->pri_email = $request->pri_email;
        $post->sec_email = $request->sec_email;
        // $post->city = $request->city;
        $post->city_id = $request->city_id;
        $post->state = $request->state;
        $post->address = $request->address;
        $post->notes = $request->notes;
        $post->med_cent_beh = $request->med_cent_beh;
        $post->web_url = $request->web_url;
        $post->update_by_id = Auth::user()->user_id;
        $post->save();

        Session::flash('success','Medical center updated!');
        return redirect()->back();
    }

    public function uploadPhoto(Request $request){
        $post = Medicalcenter::find($request->id);
        $pphoto = $post->photo;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $file_count = File::files(base_path().'/public/image/service-master');
            $filecount = 0;

            if ($file_count !== false) {
                $filecount = count($file_count);
            }

            $file_exe = $file->getClientOriginalExtension();
            $name = $filecount.'.'.$file_exe;

            $file->move(base_path().'/public/image/service-master',$name);

            $photoN = $name;
        }else{
            $photoN = $pphoto;
        }

        $post->photo = $photoN;
        $post->save();

        Session::flash('success','Photo uploaded successfully!');
        return redirect()->back();
    }

    // Medical Online Details
    public function medicalonlineIndex(){
        $pageConfigs = ['pageHeader' => false];
        return view('content.service.medical_online.index',['pageConfigs' => $pageConfigs]);
    }

    public function medicalonlineIndexJson(Request $request){
        $getData = DB::table('medicalonlines as medonline')
            ->leftJoin('qr_party_tbl as pty','pty.pty_id','=','medonline.pty_id')
            ->leftJoin('qr_candidate_tbl as cand','cand.cand_id','=','medonline.cand_id')
            ->leftJoin('users as user','user.user_id','medonline.user_id')
            ->leftJoin('medicalcenters as medicalcenter','medicalcenter.id','medonline.medicalcenter_id')
            ->where('medonline.status','=','1')
            ->orderBy('medonline.id','DESC')
            ->select('medonline.*','pty.pty_ag_name as agency',\DB::RAW("CONCAT(cand.cand_fname,' ',cand.cand_lname) as cand_full_name"),'user.name as uname','medicalcenter.name as medicalname')
            ->get();

            $data['data'] = $getData;
		    return response()->json($data);
            
    }

    public function getMedData(Request $request){

    }

    public function delMedical(Request $request){
        $post = Medicalcenter::find($request->id);
        $post->delete();

        return redirect()->back()->with('success','Medical Center deleted!');
    }
}
