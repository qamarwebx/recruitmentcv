<?php

namespace App\Http\Controllers;

use App\AdminModel\Timeline;
use App\AdminModel\VisaServiceAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;
use App\User;

class VisaServiceAccessController extends Controller
{
    public function index()
    {
        $pageConfigs = ['pageHeader' => false];

        return view('/content/apps/visa/visa-service-access-list', ['pageConfigs' => $pageConfigs]);
    }

    public function visa_acc_json(Request $request)
    {
        
        // $all_ass_visa = VisaServiceAccess::all();
        $all_ass_visa = DB::table('qr_visaservice_access_tbl as visaac')
        ->join('users as user','visaac.staff_id','=','user.user_id')
        ->select('visaac.id','visaac.emp','visaac.cand','user.name','user.email','user.username')
        ->get();
      
        $data['data'] = $all_ass_visa;
        return response()->json($data);
    }

    

    public function visa_acc_store(Request $request)
    {

        // insert data into visa access service
        $visa_acc = new VisaServiceAccess();
        $user = User::where('user_id','=',$request->input('user_id'))->where('user_type','!=','1')->first();
        
        $visa_acc->staff_id = $request->user_id;
        if ($request->cand == 1 && $request->emp == '') {
            $visa_acc->cand = 1;
            $user->user_type = 4;
            $user->save();
        }
        
        if ($request->emp == 1 && $request->cand == '') {
            $visa_acc->emp = 1;
            $user->user_type = 5;
            $user->save();
        }

        if($request->emp == 1 && $request->cand == 1){
            $visa_acc->cand = 1;
            $visa_acc->emp = 1;
            $user->user_type = 6;
            $user->save();
        }
        
        $visa_acc->user_id = Auth::user()->user_id;
        $visa_acc->save();
        
        Session::flash('success', 'Visa Service Assign Successfully');
		return redirect('master/visa-service-access/list');
    }


    public function visa_acc_edit($id)
    {
        $visa_s = VisaServiceAccess::find($id);
        $pageConfigs = ['pageHeader' => false];
        return view('/content/apps/visa/visa-service-access-edit', ['pageConfigs' => $pageConfigs,'visa_s' => $visa_s]);   
    }

    public function visa_acc_update(Request $request)
    {
        $id = $request->input('id');
        $visa_acc = VisaServiceAccess::find($id);
        $user = User::where('user_id','=',$request->user_id)->where('user_type','!=','1')->first();
        $visa_acc->staff_id = $request->user_id;

        if ($request->cand == 1 && $request->emp == '') {
            $visa_acc->cand = 1;
            $visa_acc->emp = 0;
            $user->user_type = 4;
            $user->save();
        }
        
        if ($request->emp == 1 && $request->cand == '') {
            $visa_acc->emp = 1;
            $visa_acc->cand = 0;
            $user->user_type = 5;
            $user->save();
        }

        if($request->emp == 1 && $request->cand == 1){
            $visa_acc->cand = 1;
            $visa_acc->emp = 1;
            $user->user_type = 6;
            $user->save();
        }
        if($request->emp == '' && $request->cand == ''){
            $visa_acc->cand = 0;
            $visa_acc->emp = 0;
            $user->user_type = 2;
            $user->save();
        }
        
        $visa_acc->user_id = Auth::user()->user_id;
        $visa_acc->save();
        
        Session::flash('success', 'Visa Service Assign Updated Successfully');
		return redirect('master/visa-service-access/list');
    }
}
