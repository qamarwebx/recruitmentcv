<?php

namespace App\Http\Controllers;
use App\AdminModel\Candidate;
use App\AdminModel\CandidateService;
use App\AdminModel\Nationality;
use App\AdminModel\Party;
use App\AdminModel\Employee;
use App\AdminModel\Profession;
use App\AdminModel\ReligionCast;
use App\AdminModel\Service;
use App\AdminModel\ServiceDetails;
use App\AdminModel\ServicePaymentStatus;
use App\AdminModel\ServiceStatus;
use App\AdminModel\EmployeeCandidate;
use App\AdminModel\DelEmpFileNo;
use App\AdminModel\DelEmpRefNo;
use App\AdminModel\Visa;
use App\AdminModel\Medicle;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Redirect;
use Session;


class SubmissionController extends Controller {
	

	public function submission_list(){
		$pageConfigs = ['pageHeader' => false];
		return view('/content/submission/submission', ['pageConfigs' => $pageConfigs]);
	}

	public function submission_list_json(Request $request)
	{
		extract($_POST);
		

		$result = DB::table('qr_employee_tbl as emp')
		->join('qr_employee_candidate as emp_cand', 'emp.emp_id' ,'=', 'emp_cand.emp_id')	
		->join('qr_candidate_tbl as cand', 'cand.cand_id', '=', 'emp_cand.cand_id')	
		->join('qr_services_status as st' ,'cand.cand_id' ,'=','st.cand_id')
		->join('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->join('qr_service_tbl as visa', 'emp.visa_id', '=', 'visa.ser_id')
		->select('emp.*','emp_cand.*','cand.*','pty.*','visa.*','st.*')
		->orderBy('emp.emp_id','DESC')
     	->orWhere(\DB::raw("FIND_IN_SET('".$sub_pl."',emp.sub_place)"),">",\DB::raw("'0'"))
     	->orWhere(\DB::raw("('st.".$pass_status."')"),\DB::raw("'1'"))
     	->orWhere('cand.cand_passport_no','=',$pass_no)
     	->orWhere('emp.emp_file_no','=',$file_no)
		->get();

		$data['data'] = $result;
		return response()->json($data);

	}
}