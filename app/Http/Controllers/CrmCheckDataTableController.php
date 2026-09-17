<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class CrmCheckDataTableController extends Controller
{
    public function crmcandlist(){
        return view('admin.crmback.cand.index');
    }

    public function crmcandlistJson(Request $request){
        // $posts = DB::table('qr_candidate_tbl as cand')
        // ->leftJoin('qr_employee_candidate as empcand','cand.cand_id','=','empcand.cand_id')
        // ->leftJoin('qr_services_status as ser_status','cand.cand_id','=','ser_status.cand_id')
        // ->leftJoin('qr_party_tbl as pty','cand.pty_id','=','pty.pty_id')
        // ->leftJoin('qr_candidate_status_tbl as cand_status','cand.cand_id','=','cand_status.cand_id')
        // ->leftJoin('qr_mofa_details as mofa_det','cand.cand_id','=','mofa_det.cand_id')
        // ->select('cand.cand_id','cand.cand_fname','cand.cand_mname','cand.cand_lname','cand.cand_passport_no','cand.cand_photo','empcand.emp_file_no','ser_status.mofa_paid','pty.pty_ag_name','mofa_det.mofa_no','cand_status.active_status')
		// ->get();

        $posts = DB::table('qr_candidate_tbl as cand')
        // ->leftJoin('qr_employee_candidate as empcand','cand.cand_id','=','empcand.cand_id')
        // ->leftJoin('qr_services_status as ser_status','cand.cand_id','=','ser_status.cand_id')
        ->leftJoin('qr_party_tbl as pty','cand.pty_id','=','pty.pty_id')
        ->leftJoin('qr_candidate_status_tbl as cand_status','cand.cand_id','=','cand_status.cand_id')
        // ->leftJoin('qr_mofa_details as mofa_det','cand.cand_id','=','mofa_det.cand_id')
        ->select('cand.cand_id','cand.cand_fname','cand.cand_mname','cand.cand_lname','cand.cand_passport_no','cand.cand_photo','pty.pty_ag_name','cand_status.active_status')
		->get();

        return DataTables::of($posts)->make(true);

    }

    public function crmcandlist2(){
        
        return view('admin.crmback.cand.index2');
    }
}
