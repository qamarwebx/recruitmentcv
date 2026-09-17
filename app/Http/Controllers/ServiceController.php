<?php

namespace App\Http\Controllers;

use App\AdminModel\AccessPermissionModule2;
use App\User;
use App\AdminModel\Staff;
use App\AdminModel\ServiceCandidateFilter;
use App\AdminModel\Lead;
use App\AdminModel\City;
use App\AdminModel\Country;
use App\AdminModel\Note;
use App\AdminModel\Task;
use App\AdminModel\Profession;
use App\AdminModel\Nationality;
use App\AdminModel\ReligionCast;
use App\AdminModel\Appoint;
use App\AdminModel\MofaReligion;
use App\AdminModel\CandidateService;
use App\AdminModel\Medicle;
use App\AdminModel\ServiceStatus;
use App\AdminModel\ServiceDetails;
use App\AdminModel\ServicePaymentStatus;
use App\AdminModel\Files;
use App\AdminModel\Type;
use App\AdminModel\CandidateViewNotes;
use App\AdminModel\EmployeeCandidate;
use App\AdminModel\Employee;
use App\AdminModel\Timeline;
use App\AdminModel\Label;
use App\AdminModel\Candidate;
use App\AdminModel\Service;
use App\AdminModel\Party;
use App\AdminModel\Todo;
use App\AdminModel\JobSeeker;
use Illuminate\Support\Facades\Hash;
use Session;
use Auth;
use DB;
use DataTables;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use App\AdminModel\CandidateStatus;
use App\AdminModel\MofaDetail;
use App\AdminModel\Passowner;
use App\AdminModel\CareOf;
use App\AdminModel\CandStatusNew;

use App\AdminModel\ComplCandStatus;
use App\AdminModel\VisaServiceAccess;
use App\AdminModel\CandCourier;
use App\AdminModel\CandCompFinalStatus;
use App\Mail\MofaNotification;
use App\Mail\MofaNotification2;
use App\Mail\MofaNotification3;
use Mail;
use App\Mail\VisaCopyNotification;
use App\Mail\VisaCopyNotification2;
use App\Mail\VisaCopyNotification3;
use App\AdminModel\CandIncomF;
use App\AdminModel\CandStageF;
use App\AdminModel\CandCstatusF;
use App\AdminModel\CandDocs;
use App\AdminModel\Courier;
use App\AdminModel\CourierDetails;
use App\AdminModel\CourierStatus;
use App\AdminModel\PartyAddress;

use App\Mail\MusanedRegister;
use App\Mail\MusanedRegister2;
use App\Mail\MusanedRegister3;

use App\Jobs\VisaStampedJob;
use App\Mail\MusanedRegisterAtAnother;
use App\Mail\MusanedRegisterAtAnother2;
use App\Mail\MusanedRegisterAtAnother3;

use App\AdminModel\CandidateListJpb;
use App\AdminModel\Couriernotification;
use App\AdminModel\Holdcand;
use App\Jobs\MusanedJob;
use App\Jobs\MofaPaidJob;
use App\AdminModel\Userwhatsappapi;
use App\Jobs\SendCandPassStatus;
use App\Jobs\SendCandPassStatusC;

use App\AdminModel\Courierclone;
use App\AdminModel\Crmcpass;
use App\CourerNotificationList;

use App\Jobs\CourierNotificationM;
use App\Jobs\CourierNotificationW;
use App\AdminModel\CourierServiceName;

use App\Jobs\CourierNotificationDM;
use App\Jobs\CourierNotificationDW;

use App\Jobs\CourierNotificationDRM;
use App\Jobs\CourierNotificationDRW;
// use App\AdminModel\IncompleteStage;
use App\AdminModel\BranchDetails;

use App\Jobs\CourierNotificationRRM;
use App\Jobs\CourierNotificationRRW;
	
use App\Jobs\CourierNotificationDMW;

use App\AdminModel\Emigration;
use App\AdminModel\EmigrationStatus;

use App\AdminModel\EmigrationCampaignList;
use App\Jobs\EmigrationSuccApp;
use App\Jobs\EmigrationSpnAlx;
use App\Jobs\EmigrationDocReq;
use App\Jobs\VisaSubConsulated;
use App\Jobs\EmigrationApproved;

use App\AdminModel\Branch;
use App\AdminModel\CourierReminderList;
use App\Jobs\SendCandidateRecord;
use App\Jobs\CourierDeliveratOffce;

use App\Mofabackupdetails;

class ServiceController extends Controller
{

	public function refpcandlist(Request $request){

		$perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();
		if (Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)) {
			$data_cand = DB::table('qr_candidate_tbl as cand')
			// ->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
			->leftjoin('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
			// ->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
			->leftjoin('qr_party_tbl as pty', 'cand.pty_id', '=', 'pty.pty_id')
			// ->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id')
			// ->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id')
			// ->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id')
			// ->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id')
			// ->leftjoin('users as cuser','cand.care_of_id','=','cuser.user_id')
			// ->leftjoin('users as user','cand.user_id','=','user.user_id')
			// ->select('cand.cand_id','cand.cand_photo','cand.cand_fname','cand.cand_lname','cand.cand_passport_no','emp.emp_file_no','pty.pty_ag_name','mofa_du.mofa_no','cand_status.active_status','ser_status.mofa_paid');

			->select('cand.cand_id','cand.cand_photo','cand.cand_fname','cand.cand_lname','cand.cand_passport_no','pty.pty_ag_name','ser_status.mofa_paid');
			
			// Filter and Search Based request
			

			if(isset($request->pty_id) && $request->pty_id != ''){
				$data_cand->where('cand.pty_id','=',$request->pty_id);
			}

			if(isset($request->active_status) && $request->active_status != ''){
				$data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
				$data_cand->where('cand_status.active_status','=',$request->active_status);
			}

			if(isset($request->incomplete_status) && $request->incomplete_status != ''){
				$data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
				$data_cand->where('cand_status.incomplete_status','=',$request->incomplete_status);
			}

			if(isset($request->final_status) && $request->final_status != ''){
				$data_cand->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id');
				$data_cand->WhereRaw("find_in_set('".$request->final_status."',cf_status.c_status)");
				// $data_cand->where('cf_status.c_status','=',$request->final_status);
			}

			if(isset($request->care_of_id) && $request->care_of_id != ''){
				$data_cand->where('cand.care_of_id','=',$request->care_of_id);
			}

			if(isset($request->user_id) && $request->user_id != ''){
				$data_cand->where('cand.user_id','=',$request->user_id);
			}

			if(isset($request->ser_id) && $request->ser_id != ''){
				$data_cand->where('cand.ser_id','=',$request->ser_id);
			}

			if(isset($request->visa_stamped_date) && $request->visa_stamped_date != ''){
				$data_cand->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id');
				$data_cand->whereDate('ser_det.visa_stamped_date','=',$request->visa_stamped_date);
			}

			if(isset($request->create_at) && $request->create_at != ''){
				$data_cand->whereDate('cand.created_at','=',$request->create_at);
			}

			if(isset($request->updated_at) && $request->updated_at != ''){
				$data_cand->whereDate('cand.updated_at','=',$request->updated_at);
			}
			

			if(isset($request->search_text) && $request->search_text != ''){
				// $data_cand->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id');
				// $data_cand->leftjoin('users as cuser','cand.care_of_id','=','cuser.user_id');
				// $data_cand->leftjoin('users as user','cand.user_id','=','user.user_id');
				// $data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
				// $data_cand->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id');
				// $data_cand->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id');
				$data_cand->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id');


				$data_cand->where(function($query) use($request){
					$query->orwhere('cand.cand_fname','like',"%".$request->search_text."%");
					$query->orwhere('cand.cand_mname','like',"%".$request->search_text."%");
					$query->orwhere('cand.cand_lname','like',"%".$request->search_text."%");
					$query->orwhere('cand.cand_passport_no','like',"%".$request->search_text."%");

					// $query->orwhere('emp.emp_file_no','like',"%".$request->search_text."%");
					// $query->orwhere('emp.emp_ref_no','like',"%".$request->search_text."%");
					// $query->orwhere('emp.emp_visa_no','like',"%".$request->search_text."%");
					// $query->orwhere('emp.emp_id_no','like',"%".$request->search_text."%");

					// $query->orwhere('pty.pty_ag_name','like',"%".$request->search_text."%");

					$query->orwhere('mofa_du.mofa_no','like',"%".$request->search_text."%");

					// $query->orwhere('cand_status.active_status','like',"%".$request->search_text."%");
					// $query->orwhere('cand_status.incomplete_status','like',"%".$request->search_text."%");

					// $query->orwhere('cf_status.c_status','like',"%".$request->search_text."%");

					// $query->orwhere('cuser.name','like',"%".$request->search_text."%");

					// $query->orwhere('user.name','like',"%".$request->search_text."%");
				});
			}

			if(isset($request->page_list) && $request->page_list != ''){
				$data_cand_list = $data_cand->orderBy('cand.cand_id','DESC')->paginate($request->page_list)->withQueryString();
			}else{
				$data_cand_list = $data_cand->orderBy('cand.cand_id','DESC')->paginate(10)->withQueryString();
			}
			
		}elseif(Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){
			if(isset($perms)){
				if ($perms->ser_cand_r == 1) {
					$data_cand = DB::table('qr_candidate_tbl as cand')
					// ->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
					->leftjoin('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
					// ->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
					->leftjoin('qr_party_tbl as pty', 'cand.pty_id', '=', 'pty.pty_id')
					// ->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id')
					// ->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id')
					// ->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id')
					// ->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id')
					// ->leftjoin('users as cuser','cand.care_of_id','=','cuser.user_id')
					// ->leftjoin('users as user','cand.user_id','=','user.user_id')
					// ->select('cand.cand_id','cand.cand_photo','cand.cand_fname','cand.cand_lname','cand.cand_passport_no','emp.emp_file_no','pty.pty_ag_name','mofa_du.mofa_no','cand_status.active_status','ser_status.mofa_paid');

					->select('cand.cand_id','cand.cand_photo','cand.cand_fname','cand.cand_lname','cand.cand_passport_no','pty.pty_ag_name','ser_status.mofa_paid');
			
					// Filter and Search Based request
			

					if(isset($request->pty_id) && $request->pty_id != ''){
						$data_cand->where('cand.pty_id','=',$request->pty_id);
					}

					if(isset($request->active_status) && $request->active_status != ''){
						$data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
						$data_cand->where('cand_status.active_status','=',$request->active_status);
					}

					if(isset($request->incomplete_status) && $request->incomplete_status != ''){
						$data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
						$data_cand->where('cand_status.incomplete_status','=',$request->incomplete_status);
					}

					if(isset($request->final_status) && $request->final_status != ''){
						$data_cand->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id');
						$data_cand->WhereRaw("find_in_set('".$request->final_status."',cf_status.c_status)");
						// $data_cand->where('cf_status.c_status','=',$request->final_status);
					}

					if(isset($request->care_of_id) && $request->care_of_id != ''){
						$data_cand->where('cand.care_of_id','=',$request->care_of_id);
					}

					if(isset($request->user_id) && $request->user_id != ''){
						$data_cand->where('cand.user_id','=',$request->user_id);
					}

					if(isset($request->ser_id) && $request->ser_id != ''){
						$data_cand->where('cand.ser_id','=',$request->ser_id);
					}

					if(isset($request->visa_stamped_date) && $request->visa_stamped_date != ''){
						$data_cand->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id');
						$data_cand->whereDate('ser_det.visa_stamped_date','=',$request->visa_stamped_date);
					}

					if(isset($request->create_at) && $request->create_at != ''){
						$data_cand->whereDate('cand.created_at','=',$request->create_at);
					}

					if(isset($request->updated_at) && $request->updated_at != ''){
						$data_cand->whereDate('cand.updated_at','=',$request->updated_at);
					}
			

					if(isset($request->search_text) && $request->search_text != ''){
						// $data_cand->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id');
						// $data_cand->leftjoin('users as cuser','cand.care_of_id','=','cuser.user_id');
						// $data_cand->leftjoin('users as user','cand.user_id','=','user.user_id');
						// $data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
						// $data_cand->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id');
						// $data_cand->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id');
						$data_cand->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id');


						$data_cand->where(function($query) use($request){
							$query->orwhere('cand.cand_fname','like',"%".$request->search_text."%");
							$query->orwhere('cand.cand_mname','like',"%".$request->search_text."%");
							$query->orwhere('cand.cand_lname','like',"%".$request->search_text."%");
							$query->orwhere('cand.cand_passport_no','like',"%".$request->search_text."%");

							// $query->orwhere('emp.emp_file_no','like',"%".$request->search_text."%");
							// $query->orwhere('emp.emp_ref_no','like',"%".$request->search_text."%");
							// $query->orwhere('emp.emp_visa_no','like',"%".$request->search_text."%");
							// $query->orwhere('emp.emp_id_no','like',"%".$request->search_text."%");

							// $query->orwhere('pty.pty_ag_name','like',"%".$request->search_text."%");

							$query->orwhere('mofa_du.mofa_no','like',"%".$request->search_text."%");

							// $query->orwhere('cand_status.active_status','like',"%".$request->search_text."%");
							// $query->orwhere('cand_status.incomplete_status','like',"%".$request->search_text."%");

							// $query->orwhere('cf_status.c_status','like',"%".$request->search_text."%");

							// $query->orwhere('cuser.name','like',"%".$request->search_text."%");

							// $query->orwhere('user.name','like',"%".$request->search_text."%");
						});
					}

					if(isset($request->page_list) && $request->page_list != ''){
						$data_cand_list = $data_cand->orderBy('cand.cand_id','DESC')->paginate($request->page_list)->withQueryString();
					}else{
						$data_cand_list = $data_cand->orderBy('cand.cand_id','DESC')->paginate(10)->withQueryString();
					}

				}else{
					$data_cand = DB::table('qr_candidate_tbl as cand')
					// ->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
					->leftjoin('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
					// ->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
					->leftjoin('qr_party_tbl as pty', 'cand.pty_id', '=', 'pty.pty_id')
					// ->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id')
					// ->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id')
					// ->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id')
					// ->leftjoin('qr_candidate_status_tbl as cand_status','cand.cand_id','=','cand_status.cand_id')
					// ->leftjoin('users as cuser','cand.care_of_id','=','cuser.user_id')
					// ->leftjoin('users as user','cand.user_id','=','user.user_id')
					// ->select('cand.cand_id','cand.cand_photo','cand.cand_fname','cand.cand_lname','cand.cand_passport_no','emp.emp_file_no','pty.pty_ag_name','mofa_du.mofa_no','cand_status.active_status','ser_status.mofa_paid')
					->select('cand.cand_id','cand.cand_photo','cand.cand_fname','cand.cand_lname','cand.cand_passport_no','pty.pty_ag_name','ser_status.mofa_paid')
					->where('cand.user_id','=',Auth::user()->user_id)
					->orWhere('cand.care_of_id','=',Auth::user()->user_id);

	
					// Filter and Search Based request
			

					if(isset($request->pty_id) && $request->pty_id != ''){
						$data_cand->where('cand.pty_id','=',$request->pty_id);
					}

					if(isset($request->active_status) && $request->active_status != ''){
						$data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
						$data_cand->where('cand_status.active_status','=',$request->active_status);
					}

					if(isset($request->incomplete_status) && $request->incomplete_status != ''){
						$data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
						$data_cand->where('cand_status.incomplete_status','=',$request->incomplete_status);
					}

					if(isset($request->final_status) && $request->final_status != ''){
						$data_cand->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id');
						$data_cand->WhereRaw("find_in_set('".$request->final_status."',cf_status.c_status)");
						// $data_cand->where('cf_status.c_status','=',$request->final_status);
					}

					if(isset($request->care_of_id) && $request->care_of_id != ''){
						$data_cand->where('cand.care_of_id','=',$request->care_of_id);
					}

					if(isset($request->user_id) && $request->user_id != ''){
						$data_cand->where('cand.user_id','=',$request->user_id);
					}

					if(isset($request->ser_id) && $request->ser_id != ''){
						$data_cand->where('cand.ser_id','=',$request->ser_id);
					}

					if(isset($request->visa_stamped_date) && $request->visa_stamped_date != ''){
						$data_cand->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id');
						$data_cand->whereDate('ser_det.visa_stamped_date','=',$request->visa_stamped_date);
					}

					if(isset($request->create_at) && $request->create_at != ''){
						$data_cand->whereDate('cand.created_at','=',$request->create_at);
					}

					if(isset($request->updated_at) && $request->updated_at != ''){
						$data_cand->whereDate('cand.updated_at','=',$request->updated_at);
					}
			

					if(isset($request->search_text) && $request->search_text != ''){
						// $data_cand->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id');
						// $data_cand->leftjoin('users as cuser','cand.care_of_id','=','cuser.user_id');
						// $data_cand->leftjoin('users as user','cand.user_id','=','user.user_id');
						// $data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
						// $data_cand->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id');
						// $data_cand->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id');
						$data_cand->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id');


						$data_cand->where(function($query) use($request){
							$query->orwhere('cand.cand_fname','like',"%".$request->search_text."%");
							$query->orwhere('cand.cand_mname','like',"%".$request->search_text."%");
							$query->orwhere('cand.cand_lname','like',"%".$request->search_text."%");
							$query->orwhere('cand.cand_passport_no','like',"%".$request->search_text."%");

							// $query->orwhere('emp.emp_file_no','like',"%".$request->search_text."%");
							// $query->orwhere('emp.emp_ref_no','like',"%".$request->search_text."%");
							// $query->orwhere('emp.emp_visa_no','like',"%".$request->search_text."%");
							// $query->orwhere('emp.emp_id_no','like',"%".$request->search_text."%");

							// $query->orwhere('pty.pty_ag_name','like',"%".$request->search_text."%");

							$query->orwhere('mofa_du.mofa_no','like',"%".$request->search_text."%");

							// $query->orwhere('cand_status.active_status','like',"%".$request->search_text."%");
							// $query->orwhere('cand_status.incomplete_status','like',"%".$request->search_text."%");

							// $query->orwhere('cf_status.c_status','like',"%".$request->search_text."%");

							// $query->orwhere('cuser.name','like',"%".$request->search_text."%");

							// $query->orwhere('user.name','like',"%".$request->search_text."%");
						});
					}

					if(isset($request->page_list) && $request->page_list != ''){
						$data_cand_list = $data_cand->orderBy('cand.cand_id','DESC')->paginate($request->page_list)->withQueryString();
					}else{
						$data_cand_list = $data_cand->orderBy('cand.cand_id','DESC')->paginate(10)->withQueryString();
					}
	
	
	
				}
			}else{
				$data_cand = DB::table('qr_candidate_tbl as cand')
				// ->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
				->leftjoin('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
				// ->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
				->leftjoin('qr_party_tbl as pty', 'cand.pty_id', '=', 'pty.pty_id')
				// ->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id')
				// ->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id')
				// ->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id')
				// ->leftjoin('qr_candidate_status_tbl as cand_status','cand.cand_id','=','cand_status.cand_id')
				// ->leftjoin('users as cuser','cand.care_of_id','=','cuser.user_id')
				// ->leftjoin('users as user','cand.user_id','=','user.user_id')
				// ->select('cand.cand_id','cand.cand_photo','cand.cand_fname','cand.cand_lname','cand.cand_passport_no','emp.emp_file_no','pty.pty_ag_name','mofa_du.mofa_no','cand_status.active_status','ser_status.mofa_paid')
				->select('cand.cand_id','cand.cand_photo','cand.cand_fname','cand.cand_lname','cand.cand_passport_no','pty.pty_ag_name','ser_status.mofa_paid')
				->where('cand.user_id','=',Auth::user()->user_id)
				->orWhere('cand.care_of_id','=',Auth::user()->user_id);

	
				// Filter and Search Based request
			

				if(isset($request->pty_id) && $request->pty_id != ''){
					$data_cand->where('cand.pty_id','=',$request->pty_id);
				}

				if(isset($request->active_status) && $request->active_status != ''){
					$data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
					$data_cand->where('cand_status.active_status','=',$request->active_status);
				}

				if(isset($request->incomplete_status) && $request->incomplete_status != ''){
					$data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
					$data_cand->where('cand_status.incomplete_status','=',$request->incomplete_status);
				}

				if(isset($request->final_status) && $request->final_status != ''){
					$data_cand->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id');
					$data_cand->WhereRaw("find_in_set('".$request->final_status."',cf_status.c_status)");
					// $data_cand->where('cf_status.c_status','=',$request->final_status);
				}

				if(isset($request->care_of_id) && $request->care_of_id != ''){
					$data_cand->where('cand.care_of_id','=',$request->care_of_id);
				}

				if(isset($request->user_id) && $request->user_id != ''){
					$data_cand->where('cand.user_id','=',$request->user_id);
				}

				if(isset($request->ser_id) && $request->ser_id != ''){
					$data_cand->where('cand.ser_id','=',$request->ser_id);
				}

				if(isset($request->visa_stamped_date) && $request->visa_stamped_date != ''){
					$data_cand->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id');
					$data_cand->whereDate('ser_det.visa_stamped_date','=',$request->visa_stamped_date);
				}

				if(isset($request->create_at) && $request->create_at != ''){
					$data_cand->whereDate('cand.created_at','=',$request->create_at);
				}

				if(isset($request->updated_at) && $request->updated_at != ''){
					$data_cand->whereDate('cand.updated_at','=',$request->updated_at);
				}
			

				if(isset($request->search_text) && $request->search_text != ''){
					// $data_cand->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id');
					// $data_cand->leftjoin('users as cuser','cand.care_of_id','=','cuser.user_id');
					// $data_cand->leftjoin('users as user','cand.user_id','=','user.user_id');
					// $data_cand->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id');
					// $data_cand->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id');
					// $data_cand->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id');
					$data_cand->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id');


					$data_cand->where(function($query) use($request){
						$query->orwhere('cand.cand_fname','like',"%".$request->search_text."%");
						$query->orwhere('cand.cand_mname','like',"%".$request->search_text."%");
						$query->orwhere('cand.cand_lname','like',"%".$request->search_text."%");
						$query->orwhere('cand.cand_passport_no','like',"%".$request->search_text."%");

						// $query->orwhere('emp.emp_file_no','like',"%".$request->search_text."%");
						// $query->orwhere('emp.emp_ref_no','like',"%".$request->search_text."%");
						// $query->orwhere('emp.emp_visa_no','like',"%".$request->search_text."%");
						// $query->orwhere('emp.emp_id_no','like',"%".$request->search_text."%");

						// $query->orwhere('pty.pty_ag_name','like',"%".$request->search_text."%");

						$query->orwhere('mofa_du.mofa_no','like',"%".$request->search_text."%");

						// $query->orwhere('cand_status.active_status','like',"%".$request->search_text."%");
						// $query->orwhere('cand_status.incomplete_status','like',"%".$request->search_text."%");

						// $query->orwhere('cf_status.c_status','like',"%".$request->search_text."%");

						// $query->orwhere('cuser.name','like',"%".$request->search_text."%");

						// $query->orwhere('user.name','like',"%".$request->search_text."%");
					});
				}

				if(isset($request->page_list) && $request->page_list != ''){
					$data_cand_list = $data_cand->orderBy('cand.cand_id','DESC')->paginate($request->page_list)->withQueryString();
				}else{
					$data_cand_list = $data_cand->orderBy('cand.cand_id','DESC')->paginate(10)->withQueryString();
				}
	
			}
		}

		if($request->ajax()){
			return view('content.service.loadp',['data_cands' => $data_cand_list])->render();
		}

		$pageConfigs = ['pageHeader' => false];
		return view('content.service.candidatep',['pageConfigs' => $pageConfigs,'data_cands' => $data_cand_list]);
	}
	
	public function candidate_list(){
		$pageConfigs = ['pageHeader' => false];
		return view('/content/service/candidate', ['pageConfigs' => $pageConfigs]);
	}

	public function updateFilter(Request $request){
		$partyf = $request->partyf;
		$stagef = $request->stagef;
		$incompletef = $request->incompletef;
		$statusf = $request->statusf;
		$care_offf = $request->care_offf;
		$created_by = $request->created_by;
		$service_typef = $request->service_typef;
		$visa_stamped_datef = $request->visa_stamped_datef;
		$updated_at_ff = $request->updated_at_ff;
		$created_at_ff = $request->created_at_ff;
		$all_documentf = $request->all_documentf;
		$new_documentf = $request->new_documentf;
		$for_mofaf = $request->for_mofaf;
		$mofa_unpaidf = $request->mofa_unpaidf;
		$awaited_passportf = $request->awaited_passportf;
		$mediclef = $request->mediclef;
		$for_submissionf = $request->for_submissionf;
		$submission_readyf = $request->submission_readyf;
		$holdf = $request->holdf;
		$submittedf = $request->submittedf;
		$rejectionf = $request->rejectionf;
		$emigration_appliedf = $request->emigration_appliedf;
		$donef = $request->donef;
		$cancelledf = $request->cancelledf;
		// Check User is exists or Not
		$user = ServiceCandidateFilter::where('user_id','=',Auth::user()->user_id)->count();
		if($user > 0){

			$updatedF = ServiceCandidateFilter::where('user_id','=',Auth::user()->user_id)->first();
			$updatedF->user_id = Auth::user()->user_id;
			if($partyf == 1){
				$updatedF->party = '1';
			}else{
				$updatedF->party = '0';
			}
			if($stagef == 1){
				$updatedF->stage = '1';
			}else{
				$updatedF->stage = '0';
			}
			if($incompletef == 1){
				$updatedF->incomplete = '1';
			}else{
				$updatedF->incomplete = '0';
			}
			if($statusf == 1){
				$updatedF->status = '1';
			}else{
				$updatedF->status = '0';
			}
			if($care_offf == 1){
				$updatedF->care_off = '1';
			}else{
				$updatedF->care_off = '0';
			}
			if($created_by == 1){
				$updatedF->created_by = '1';
			}else{
				$updatedF->created_by = '0';
			}
			if($service_typef == 1){
				$updatedF->service_type = '1';
			}else{
				$updatedF->service_type = '0';
			}
			if($visa_stamped_datef == 1){
				$updatedF->visa_stamped_date = '1';
			}else{
				$updatedF->visa_stamped_date = '0';
			}
			if($updated_at_ff == 1){
				$updatedF->updated_at_f = '1';
			}else{
				$updatedF->updated_at_f = '0';
			}
			if($created_at_ff == 1){
				$updatedF->created_at_f = '1';
			}else{
				$updatedF->created_at_f = '0';
			}
			if($all_documentf == 1){
				$updatedF->all_document = '1';
			}else{
				$updatedF->all_document = '0';
			}
			if($new_documentf == 1){
				$updatedF->new_document = '1';
			}else{
				$updatedF->new_document = '0';
			}
			if($for_mofaf == 1){
				$updatedF->for_mofa = '1';
			}else{
				$updatedF->for_mofa = '0';
			}
			if($mofa_unpaidf == 1){
				$updatedF->mofa_unpaid = '1';
			}else{
				$updatedF->mofa_unpaid = '0';
			}
			if($awaited_passportf == 1){
				$updatedF->awaited_passport = '1';
			}else{
				$updatedF->awaited_passport = '0';
			}
			if($mediclef == 1){
				$updatedF->medicle = '1';
			}else{
				$updatedF->medicle = '0';
			}
			if($for_submissionf == 1){
				$updatedF->for_submission = '1';
			}else{
				$updatedF->for_submission = '0';
			}
			if($submission_readyf == 1){
				$updatedF->submission_ready = '1';
			}else{
				$updatedF->submission_ready = '0';
			}
			if($holdf == 1){
				$updatedF->hold = '1';
			}else{
				$updatedF->hold = '0';
			}
			if($submittedf == 1){
				$updatedF->submitted = '1';
			}else{
				$updatedF->submitted = '0';
			}
			if($rejectionf == 1){
				$updatedF->rejection = '1';
			}else{
				$updatedF->rejection = '0';
			}
			if($emigration_appliedf == 1){
				$updatedF->emigration_applied = '1';
			}else{
				$updatedF->emigration_applied = '0';
			}
			if($donef == 1){
				$updatedF->done = '1';
			}else{
				$updatedF->done = '0';
			}
			if($cancelledf == 1){
				$updatedF->cancelled = '1';
			}else{
				$updatedF->cancelled = '0';
			}

			$updatedF->save();
    		return response()->json('success');


		}else{
			$createdF = new ServiceCandidateFilter();
			$createdF->user_id = Auth::user()->user_id;
			if($partyf == 1){
				$createdF->party = '1';
			}else{
				$createdF->party = '0';
			}
			if($stagef == 1){
				$createdF->stage = '1';
			}else{
				$createdF->stage = '0';
			}
			if($incompletef == 1){
				$createdF->incomplete = '1';
			}else{
				$createdF->incomplete = '0';
			}
			if($statusf == 1){
				$createdF->status = '1';
			}else{
				$createdF->status = '0';
			}
			if($care_offf == 1){
				$createdF->care_off = '1';
			}else{
				$createdF->care_off = '0';
			}

			if($created_by == 1){
				$createdF->created_by = '1';
			}else{
				$createdF->created_by = '0';
			}

			if($service_typef == 1){
				$createdF->service_type = '1';
			}else{
				$createdF->service_type = '0';
			}
			if($visa_stamped_datef == 1){
				$createdF->visa_stamped_date = '1';
			}else{
				$createdF->visa_stamped_date = '0';
			}
			if($updated_at_ff == 1){
				$createdF->updated_at_f = '1';
			}else{
				$createdF->updated_at_f = '0';
			}
			if($created_at_ff == 1){
				$createdF->created_at_f = '1';
			}else{
				$createdF->created_at_f = '0';
			}
			if($all_documentf == 1){
				$createdF->all_document = '1';
			}else{
				$createdF->all_document = '0';
			}
			if($new_documentf == 1){
				$createdF->new_document = '1';
			}else{
				$createdF->new_document = '0';
			}
			if($for_mofaf == 1){
				$createdF->for_mofa = '1';
			}else{
				$createdF->for_mofa = '0';
			}
			if($mofa_unpaidf == 1){
				$createdF->mofa_unpaid = '1';
			}else{
				$createdF->mofa_unpaid = '0';
			}
			if($awaited_passportf == 1){
				$createdF->awaited_passport = '1';
			}else{
				$createdF->awaited_passport = '0';
			}
			if($mediclef == 1){
				$createdF->medicle = '1';
			}else{
				$createdF->medicle = '0';
			}
			if($for_submissionf == 1){
				$createdF->for_submission = '1';
			}else{
				$createdF->for_submission = '0';
			}
			if($submission_readyf == 1){
				$createdF->submission_ready = '1';
			}else{
				$createdF->submission_ready = '0';
			}
			if($holdf == 1){
				$createdF->hold = '1';
			}else{
				$createdF->hold = '0';
			}
			if($submittedf == 1){
				$createdF->submitted = '1';
			}else{
				$createdF->submitted = '0';
			}
			if($rejectionf == 1){
				$createdF->rejection = '1';
			}else{
				$createdF->rejection = '0';
			}
			if($emigration_appliedf == 1){
				$createdF->emigration_applied = '1';
			}else{
				$createdF->emigration_applied = '0';
			}
			if($donef == 1){
				$createdF->done = '1';
			}else{
				$createdF->done = '0';
			}
			if($cancelledf == 1){
				$createdF->cancelled = '1';
			}else{
				$createdF->cancelled = '0';
			}

			$createdF->save();
    		return response()->json('success');
		}
	}

	public function dailytxn()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('content.service-master.daily_txn',['pageConfigs' => $pageConfigs]);
    }

    public function dailytxnJson(Request $request)
    {
        $post = DB::table('daily_transactions as dtxn')
            ->leftjoin('qr_party_tbl as party','party.pty_id','=','dtxn.pty_id')
            ->leftjoin('payment_methods as pmet','pmet.id','=','dtxn.pmethod_id')
            ->leftjoin('payment_for_accs as pmfor','pmfor.id','=','dtxn.paymentf_id')
            ->leftjoin('depositi_accounts as depacc','depacc.id','=','dtxn.depositi_id')
            ->leftjoin('user_status_accs as userSt','userSt.id','=','dtxn.userst_id')
            ->leftjoin('admin_status_accs as adminSt','adminSt.id','=','dtxn.adminst_id')
            ->leftjoin('users as userC','userC.user_id','=','dtxn.careoff_id')
            ->leftjoin('users as user','user.user_id','=','dtxn.user_id')
            ->leftjoin('user as userUp','userUp.user_id','=','dtxn.updateby_id')
            ->leftjoin('qr_branch_details as branch','branch.br_id','=','dtxn.br_id')
            ->select('dtxn.*','party.pty_ag_name','pmet.name as pmname','pmfor.name as pmfname','depacc.account_holder_name','userSt.name as ustname','adminSt.name as astname','userC.name as ucname','user.name as uname','userUp.name as upname','branch.br_name')
            ->get();

        

            // $data['data'] = $post;
            // return response()->json($data);
            return DataTables::of($post)->toJson();
    }

    public function dailytxnStore(Request $request)
    {
        // check and get file
        if ($request->hasFile('slip')) {
            $file = $request->file('slip');
            $name = time().'_'.$file->getClientOriginalName();
            $file->move(base_path().'/public/image/accounts',$name);
            $slip = $name;
        }else{
            $slip = '';
        }

        // Get Br_id
        if (Auth::user()->user_type != 1) {
            $branch = Staff::where('staff_id','=',Auth::user()->user_id)->first();
            $branch_id = $branch->staff_branch;
        } else {
            $branch_id = null;
        }
        

        $post = new DailyTransaction();
        $post->pty_id = $request->pty_id;
        $post->desc = $request->desc;
        $post->candidate_name = $request->candidate_name;
        $post->cand_pass_no = $request->cand_pass_no;
        $post->invoice_no = $request->invoice_no;
        $post->amount = $request->amount;
        $post->txn_utr_no = $request->txn_utr_no;
        $post->txn_date = $request->txn_date;
        $post->pmethod_id = $request->pmethod_id;
        $post->depositi_id = $request->depositi_id;
        $post->paymentf_id = $request->paymentf_id;
        $post->careoff_id = $request->careoff_id;
        $post->br_id = $branch_id;
        $post->user_id = Auth::user()->user_id;
        $post->userst_id = $request->userst_id;
        $post->adminst_id = $request->adminst_id;
        $post->slip = $slip;
        $post->save();

        Session::flash('success','Daily transaction created!');
        return redirect()->back();
    }


	public function candidate_delete(Request $request){
		extract($_POST);	
		if(Auth::user()->user_type==1){
			$c = Candidate::find($id);
			// Find in employee candidate table
			$ec = EmployeeCandidate::where('cand_id','=',$id)->first();
			//  Find in Service Details
			$sc = ServiceDetails::where('cand_id','=',$id)->first();
			// Find in Service Status
			$ssc = ServiceStatus::where('cand_id','=',$id)->first();
			// Find Service Payment Status
			$spc = ServicePaymentStatus::where('cand_id','=',$id)->first();
			// Find in Candidate Service
			$csc = CandidateService::where('cand_id','=',$id)->first();
			// Find in Candidate Status
			$cstc = CandidateStatus::where('cand_id','=',$id)->first();
			// find in candidate new status
			$cfstc = ComplCandStatus::where('cand_id','=',$id)->first();
			// Find FInal Status
			$fnc = CandCompFinalStatus::where('cand_id','=',$id)->get();
			// delete data from mofa_table
			$mfd = MofaDetail::where('cand_id','=',$id)->first();
			// dd($sc);
			if(isset($mfd)){
				$mfd->delete();
			}

			if (isset($fnc)) {
				foreach ($fnc as $fn) {
					$fn->delete();
				}
			}
			
			if($ec != ''){
				$ec->delete();
			}

			if($sc != ''){
				$sc->delete();
			}

			if($ssc != ''){
				$ssc->delete();
			}
			
			if($spc != ''){
				$spc->delete();
			}
			
			if($csc != ''){
				$csc->delete();
			}
			
			if($cstc != ''){
				$cstc->delete();
			}

			if($cfstc != ''){
				$cfstc->delete();
			}

			if($c !=''){
				$c->delete();
			}
			
			Session::flash('success', 'Candidate deleted successfully !');
		}
		else if(Auth::user()->user_type!=1){
			$c = Candidate::find($id);
			$c->status=0;
			Session::flash('success', 'Candidate deleted successfully !');
		}

		// Create Activity
		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $id;
		$timeline->subject = 'Candidate Delete';
		$timeline->time = date("h:i:sa");
		$timeline->user_id = Auth::user()->user_id;
		$timeline->status = 0;
		$timeline->save();

		return redirect()->back();

		// $pageConfigs = ['pageHeader' => false];
		// return view('/content/service/candidate', ['pageConfigs' => $pageConfigs]);
	}

	public function candidate_list_json(Request $request){
		$perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();

		if (Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)) {
			
			$data_cand = DB::table('qr_candidate_tbl as cand')
			->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
			->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
			->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
			->leftjoin('qr_party_tbl as pty', 'cand.pty_id', '=', 'pty.pty_id')
			->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
			->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
			->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id')
			->leftjoin('qr_candidate_status_tbl as cand_status','cand.cand_id','=','cand_status.cand_id')
			->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id')
			->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id')
			->select('cand.cand_id','cand.user_id','cand.pty_id','cand.ser_id','cand.care_of_id as careof','cand_status.updated_at','cf_status.c_status','cand.cand_passport_no','mofa_du.mofa_no','emp.emp_file_no','cand.created_at','cand_status.active_status','cand_status.incomplete_status','pty.pty_ag_name','cand.cand_fname','cand.cand_lname','cand.cand_mname','emp.emp_id_no','ser_det.mofa as mofa_data','ser_det.visa_stamped_date','ser_status.mofa_paid','emp.emp_visa_no','pf.prof_eng_name',\DB::raw("CONCAT(cand.cand_fname,' ',cand.cand_lname) as cand_full_name",'ser_status.musaned')) 
			->get();

		} elseif (Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)) {
			
			if(isset($perms)){
				if ($perms->ser_cand_r == 1) {
					$data_cand = DB::table('qr_candidate_tbl as cand')
					->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
					->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
					->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
					->leftjoin('qr_party_tbl as pty', 'cand.pty_id', '=', 'pty.pty_id')
					->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
					->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
					->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id')
					->leftjoin('qr_candidate_status_tbl as cand_status','cand.cand_id','=','cand_status.cand_id')
					->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id')
					->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id')
					->select('cand.cand_id','cand.user_id','cand.pty_id','cand.ser_id','cand.care_of_id as careof','cand_status.updated_at','ser_det.visa_stamped_date','cf_status.c_status','cand.cand_passport_no','mofa_du.mofa_no','emp.emp_file_no','cand.created_at','cand_status.active_status','cand_status.incomplete_status','pty.pty_ag_name','cand.cand_fname','cand.cand_lname','cand.cand_mname','emp.emp_id_no','ser_det.mofa as mofa_data','ser_status.mofa_paid','emp.emp_visa_no','pf.prof_eng_name',\DB::raw("CONCAT(cand.cand_fname,' ',cand.cand_lname) as cand_full_name",'ser_status.musaned')) 
					->get();
				} else {
					$data_cand = DB::table('qr_candidate_tbl as cand')
					->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
					->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
					->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
					->leftjoin('qr_party_tbl as pty', 'cand.pty_id', '=', 'pty.pty_id')
					->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
					->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
					->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id')
					->leftjoin('qr_candidate_status_tbl as cand_status','cand.cand_id','=','cand_status.cand_id')
					->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id')
					->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id')
					->select('cand.cand_id','cand.user_id','cand.pty_id','cand.ser_id','cand.care_of_id as careof','cand_status.updated_at','ser_det.visa_stamped_date','cf_status.c_status','cand.cand_passport_no','mofa_du.mofa_no','emp.emp_file_no','cand.created_at','cand_status.active_status','cand_status.incomplete_status','pty.pty_ag_name','cand.cand_fname','cand.cand_lname','cand.cand_mname','emp.emp_id_no','ser_det.mofa as mofa_data','ser_status.mofa_paid','emp.emp_visa_no','pf.prof_eng_name',\DB::raw("CONCAT(cand.cand_fname,' ',cand.cand_lname) as cand_full_name",'ser_status.musaned')) 
					->where('cand.user_id','=',Auth::user()->user_id)
					->orWhere('cand.care_of_id','=',Auth::user()->user_id)
					->get();
				}
			}else{
				$data_cand = DB::table('qr_candidate_tbl as cand')
				->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
				->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
				->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
				->leftjoin('qr_party_tbl as pty', 'cand.pty_id', '=', 'pty.pty_id')
				->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
				->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
				->leftjoin('qr_services_details as ser_det', 'cand.cand_id', '=', 'ser_det.cand_id')
				->leftjoin('qr_candidate_status_tbl as cand_status','cand.cand_id','=','cand_status.cand_id')
				->leftjoin('qr_mofa_details as mofa_du','cand.cand_id','=','mofa_du.cand_id')
				->leftjoin('qr_cand_final_status as cf_status','cand.cand_id','=','cf_status.cand_id')
				->select('cand.cand_id','cand.user_id','cand.pty_id','cand.ser_id','cand.care_of_id as careof','cand_status.updated_at','ser_det.visa_stamped_date','cf_status.c_status','cand.cand_passport_no','mofa_du.mofa_no','emp.emp_file_no','cand.created_at','cand_status.active_status','cand_status.incomplete_status','pty.pty_ag_name','cand.cand_fname','cand.cand_lname','cand.cand_mname','emp.emp_id_no','ser_det.mofa as mofa_data','ser_status.mofa_paid','emp.emp_visa_no','pf.prof_eng_name',\DB::raw("CONCAT(cand.cand_fname,' ',cand.cand_lname) as cand_full_name",'ser_status.musaned')) 
				->where('cand.user_id','=',Auth::user()->user_id)
				->orWhere('cand.care_of_id','=',Auth::user()->user_id)
				->get();
			}
			
		}
		
		// Check User Type
		$chk_user_type = User::where('user_id','=',Auth::user()->user_id)->first();
		// Fetch Data According to User Type

		// return DataTables::of($data_cand)->toJson();

		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_check_passport(Request $request){
		$chk = Candidate::where('cand_passport_no','=',$request->input('pass_no'))->first();

		if($chk){
			return response()->json('1');
		}else{

		}
	}

	public function candidate_check_mofa(Request $request){
		$chk = $request->input('mofa_no');
		// $chk = Candidate::where('cand_passport_no','=',$request->input('pass_no'))->first();
		$servds = ServiceDetails::all();
		foreach($servds as $servd){
			$mofa_d = unserialize($servd->mofa);
			if($mofa_d['mofa_no'] == $chk){
				return response()->json('1');
			}else{

			}
		}
		// if($chk){
		// 	return response()->json('1');
		// }else{

		// }
	}

	public function candidate_newdocument_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.mofa', '=', '0')
		->where('ser_status.new_document', '=', '0')
		->where('cand.created_at', '>', Carbon::now()->subDays(14))
		->where('cand.created_at', '>', Carbon::now()->subDays(14))
		->get();

		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_formofa_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.mofa_paid', '=', '0')
		->where('ser_status.mofa_unpaid', '=', '0')
		->where('ser_status.new_document', '=', '1')
		->get();

		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_mofaunpaid_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.mofa_unpaid', '=', '1')
		->where('ser_status.mofa_paid', '=', '0')
		->get();

		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_mofapaid_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.medicle', '=', '0')
		->where('ser_status.mofa_paid', '=', '1')
		->get();

		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_passportcopy_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.passport_copy', '=', '1')
		->where('ser_status.medicle', '=', '0')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_medicle_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.mofa', '=', '1')
		->where('ser_status.medicle', '=', '0')
		->where('ser_status.awaited_passport', '=', '0')
		->get();

		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_tosubmission_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.submission', '=', '0')
		->where('ser_status.awaited_passport', '=', '1')
		->where('ser_status.hold', '=', '0')
		->where('ser_status.submitted', '=', '0')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_awaitedpassport_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.awaited_passport', '=', '0')
		->where('ser_status.medicle', '=', '1')
		->where('ser_status.hold', '=', '0')
		->where('ser_status.submitted', '=', '0')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_hold_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.hold', '=', '1')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_submitted_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.submitted', '=', '1')
		->where('ser_status.emi_apply', '=', '0')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_rejection_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.rejected', '=', '1')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_foremigration_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.emi_apply', '=', '1')
		->where('ser_status.emi_done', '=', '0')
		->where('ser_status.emi_reject', '=', '0')
		->where('ser_status.emi_reject_next', '=', '0')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}


	public function candidate_inemigration_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.emi_done', '=', '1')
		->where('ser_status.emi_reject', '=', '0')
		->where('ser_status.emi_reject_next', '=', '0')
		->where('ser_status.flight_book', '=', '0')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_emigrationreject_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.emi_reject', '=', '1')
		->orWhere('ser_status.emi_reject_next', '=', '1')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_done_list_json(Request $request){
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'emp.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('emp.*','emp.*','emp_cand.*','cand.*')
		->orderBy('cand.cand_id','DESC')
		->where('ser_status.work_done', '=', '1')
		->get();
		$data['data'] = $data_cand;
		return response()->json($data);
	}

	public function candidate_view($type,$id){
		$cand_data = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_services_status as sers','cand.cand_id','=','sers.cand_id')
		->leftjoin('qr_services_details as ser', 'cand.cand_id', '=', 'ser.cand_id')
		->leftjoin('qr_candidate_service as cand_ser', 'cand.cand_id', '=', 'cand_ser.cand_id')
		->leftjoin('qr_service_tbl as vs', 'vs.ser_id', '=', 'cand.cand_visa_type')
		->leftjoin('qr_party_tbl as pty', 'pty.pty_id', '=', 'cand.pty_id')
		->leftjoin('qr_nationality_tbl as nt', 'nt.nat_id', '=', 'cand.nat_id')
		->leftjoin('qr_religion_cast_tbl as cst', 'cst.rel_cst_id', '=', 'cand.rel_cst_id')
		->leftjoin('qr_profession_tbl as prof', 'prof.prof_id', '=', 'cand.prof_id')
		->leftjoin('qr_candidate_place_issue as cand_pl_issue', 'cand.cand_place_issue', '=', 'cand_pl_issue.id')
		->leftjoin('qr_employee_candidate as empc','cand.cand_id','=','empc.cand_id')
		->select('cand.*','cand.cand_visa_type as visa_id','ser.new_document as newDocs','ser.mofa_photo','ser.pass_dc_q_id','ser.updated_at as serupd','ser.mofa as mofa','ser.medicle as medicle','ser.medicle_online_update as medup_data','cand.created_at as cand_created','cand_ser.cand_passport_type','cand_ser.cand_visa_type','cand_ser.cand_service_type','vs.ser_name as visa_name','pty.*','nt.*','cst.*','prof.*','cand_pl_issue.*','sers.passport_original','ser.medical_report','ser.application_fee_date','ser.medical_fee_date','ser.musaned_date','ser.musaned_doc','ser.musaned_photo','ser.musaned_required','ser.musaned_user_by','empc.*')
		->where('cand.cand_id', '=', $id)
		->first();

		// Variable for Seperate
		$cand_pass_d = DB::table('qr_candidate_tbl')->where('cand_id','=',$id)->first();
		
		// Mofa Backs Details
		$mofabacks = Mofabackupdetails::where('cand_id','=',$id)->orderBy('id','DESC')->get();
		

		$pageConfigs = ['pageHeader' => false];
		return view('/content/service/candidate-view', ['mofabacks' => $mofabacks,'cand_data' => $cand_data,'cand_pass_d' => $cand_pass_d,'type' => $type,'pageConfigs' => $pageConfigs]);
	}

	public function checkMofaexist(Request $request){
		$mofa_no = $request->mofa_no;

		$checkbackmof = Mofabackupdetails::where('mofa_no','=',$mofa_no)->count();
		$mofaDetails = DB::table('qr_mofa_details')->where('mofa_no','=',$mofa_no)->count();
		if($checkbackmof > 0 || $mofaDetails > 0){
			$data = ['exists' => 1];
		}else{
			$data = ['exists' => 2];
		}

		return response()->json($data);
	}

	public function viewAddmofa(Request $request,$id){
		$mofa_no = $request->mofa_no;

		$cand = Candidate::find($id);
		$cand_ser_det = ServiceDetails::where('cand_id','=',$id)->first();
		$mofa_detail = MofaDetail::where('cand_id','=',$id)->first();
		$mofabackups = Mofabackupdetails::where('cand_id','=',$id)->get();

		$mofa_data = unserialize($cand_ser_det->mofa);
		$mofa_data['mofa_no'] = $mofa_no;
		$mofa_data_ser = serialize($mofa_data);

		// update in service details
		$cand_ser_det->mofa = $mofa_data_ser;
		$cand_ser_det->save();

		// Update in mofa details
		$mofa_detail->mofa_no = $mofa_no;
		$mofa_detail->save();

		// Inactive previous status of mofabackups
		foreach($mofabackups as $mofabackup){
			$mofabackup->status = false;
			$mofabackup->save();
		}

		// Add new mofa no in backupds
		$post = new Mofabackupdetails();
		$post->mofa_no = $mofa_no;
		$post->mofa_data = $mofa_data_ser;
		$post->mofa_issue_date = date('Y-m-d h:i');
		$post->mofa_religion = $cand->rel_cst_id;
		$post->application_fee = true;
		$post->medical_fee = 1;
		$post->application_paid_date = date('Y-m-d h:i');
		$post->medical_paid_date = date('Y-m-d h:i');
		$post->user_id = Auth::user()->user_id;
		$post->mofa_paid = true;
		$post->cand_id = $id;
		$post->save();

		return redirect()->back()->with('success','Mofa number added!');
	}

	public function update_cand_phot(Request $request){
		$cand_id = $request->input('cand_id');
		$cand_data = Candidate::find($cand_id);
		$cand_photo = $cand_data->cand_photo;

		if ($request->hasFile('cand_photo')) {
			$file = $request->file('cand_photo');
			$file_count = File::files(base_path().'/public/image/service-candidate');
			$filecount = 0;

			if ($file_count !== false) {
				$filecount = count($file_count);
			}
			$file_exe = $file->getClientOriginalExtension();
			$name = $filecount . '.' . $file_exe;

			$file->move(base_path().'/public/image/service-candidate', $name);
			// $file->move(base_path().'/public/image/service-candidate', $name);
			$candidate_photo = $name;

		} else {
			$candidate_photo = $cand_photo;
		}

		$up_date = Candidate::where('cand_id','=',$cand_id)->first();
		$up_date->cand_photo = $candidate_photo;
		$up_date->save();
		// update time line
		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_id;
		$timeline->subject = 'Udate Candidate Photo';
		$timeline->time = date("h:i:sa");
		$timeline->user_id = Auth::user()->user_id;
		$timeline->status = 1;
		$timeline->save();
		Session::flash('success', 'Candidate Photo updated successfully !');
		return redirect()->back();
	}

	

	public function uploadOtherDoc(Request $request){

		$cand_id = $request->input('cand_id');
		$cand_d = Candidate::where('cand_id','=',$cand_id)->first();
		$pass_no = $cand_d->cand_passport_no;

		// request data
		$file_label = $request->input('file_label');
		if($file_label == 'Photo'){
			$folder1 = $pass_no.'IMG';
		}elseif($file_label == 'Passport Copy'){
			$folder1 = $pass_no.'PP';
		}elseif ($file_label == 'Visa Copy') {
			$folder1 = $pass_no.'Visadocs';
		}elseif ($file_label == 'Musaned CV') {
			$folder1 = $pass_no.'MusCV';
		}elseif ($file_label == 'Mofa Screenshot') {
			$folder1 = $pass_no.'MOFA';
		}elseif ($file_label == 'Other Documents') {
			$folder1 = $pass_no.'Others';
		}
		
		if($request->hasFile('other_docs')){
			$files = $request->file('other_docs');
			foreach ($files as $file) {
				// get file extension			
				$file_name = $pass_no.'-'.time().'-'.$file->getClientOriginalName();
				$path_folder = public_path('file/candidate/'.$folder1.'/');
				// if(!FIle::exists($path_folder)){
					// File::makeDirectory($path_folder,775,true,true);
					// $file->move(base_path().$path_folder,$name);
					
					$file->move(base_path().'/public/image/service-candidate',$file_name);
					// $other_doc[] = $name;

				// }

				$cand_up = new CandDocs();
				$cand_up->cand_id = $cand_id;
				$cand_up->file_lable = $file_label;
				$cand_up->user_id = Auth::user()->user_id;
				$cand_up->cand_pass_no = $pass_no;
				$cand_up->file_destination = $folder1;
				$cand_up->docs_file = $file_name;
				$cand_up->save();
			}
		}

		
		// if($request->hasFile('other_doc')){
		// 	$files = $request->file('other_doc');
		// 	foreach ($files as $file) {
		// 		$file_count = File::files(base_path().'/public/image/service-candidate');
		// 		$filecount = 0;
		// 		if ($file_count !== false) {
		// 			$filecount = count($file_count);
		// 		}
		// 		$file_exe = $file->getClientOriginalExtension();
		// 		$name = $filecount . '.' . $file_exe;
		// 		$file->move(base_path().'/public/image/service-candidate', $name);
		// 		$other_doc[] = $name;

		// 		$cand_d->cand_id = $cand_id;
		// 		$cand_d->file_lable = $file_label;
		// 		$cand_d->user_id = Auth::user()->user_id;
		// 		$cand_d->cand_pass_no = $pass_no;
		// 		$cand_d->file_destination = $cand_id;
		// 		$cand_d->docs_file = implode(',', $other_doc);
		// 		$cand_d->save();

		// 	}
		// }



		// $cand_d->cand_id = $cand_id;
		// $cand_d->file_lable = $file_label;
		// $cand_d->user_id = Auth::user()->user_id;
		// $cand_d->cand_pass_no = $pass_no;
		// $cand_d->file_destination = $cand_id;
		// $cand_d->docs_file = implode(',', $other_doc);
		// $cand_d->save();
		Session::flash('success', 'Other Document Successfully !');
		return redirect()->back();
	}

	public function candidate_store(Request $request){
		
		// Get country
		$country = Nationality::where('nat_name','=','india')->first();

		if ($request->hasFile('cand_photo')) {
			$file = $request->file('cand_photo');
			foreach ($file as  $file) {
				$file_count = File::files(base_path().'/public/image/service-candidate');
				$filecount = 0;
				if ($file_count !== false) {
					$filecount = count($file_count);
				}
				$file_exe = $file->getClientOriginalExtension();
				$name = $filecount . '.' . $file_exe;
				$file->move(base_path().'/public/image/service-candidate', $name);
				// $file->move(base_path().'/public/status-images/mofa_images', $name);
				$candidate_photo[] = $name;
			}
		}else {
			$candidate_photo[] = '';
		}

		if ($request->hasFile('cand_passport_doc')) {
			$file = $request->file('cand_passport_doc');
			foreach ($file as  $file) {
				$file_count = File::files(base_path().'/public/image/service-candidate');
				$filecount = 0;

				if ($file_count !== false) {
					$filecount = count($file_count);
				}
				$file_exe = $file->getClientOriginalExtension();
				$name = $filecount . '.' . $file_exe;

				$file->move(base_path().'/public/image/service-candidate', $name);
				$candidate_passport[] = $name;
			}
		}else {
			$candidate_passport[] = '';
		}
		/**/


		if ($request->hasFile('cand_cv')) {
			$file = $request->file('cand_cv');
			foreach ($file as  $file) {
				$file_count = File::files(base_path().'/public/image/service-candidate');
				$filecount = 0;

				if ($file_count !== false) {
					$filecount = count($file_count);
				}
				$file_exe = $file->getClientOriginalExtension();
				$name = $filecount . '.' . $file_exe;

				$file->move(base_path().'/public/image/service-candidate', $name);
				$candidate_cv[] = $name;
			}
		} else {
			$candidate_cv[] = '';
		}
		/**/

		if ($request->hasFile('cand_doc')) {
			$file = $request->file('cand_doc');
			foreach ($file as  $file) {
				$file_count = File::files(base_path().'/public/image/service-candidate');
				$filecount = 0;

				if ($file_count !== false) {
					$filecount = count($file_count);
				}
				$file_exe = $file->getClientOriginalExtension();
				$name = $filecount . '.' . $file_exe;

				$file->move(base_path().'/public/image/service-candidate', $name);
				// $file->move(base_path().'/public/status-images/passport_documents', $name);
				$candidate_doc[] = $name;
			}
		} else {
			$candidate_doc[] = '';
		}
        //
		// Insert Record Into qr_candidate_tbl
		$candidate = new Candidate();
		$candidate->pty_id = $request->input('pty_id');
		$candidate->care_of_id = $request->input('care_of_id');
		$candidate->ser_id = $request->input('ser_id') ? $request->input('ser_id') :'';
		$candidate->cand_passport_type = $request->input('cand_passport_type');
		$candidate->cand_fname = $request->input('cand_fname');
		$candidate->cand_mname = $request->input('cand_mname');
		$candidate->cand_lname = $request->input('cand_lname');
		$candidate->cand_passport_no = $request->input('cand_passport_no');
		$candidate->cand_place_birth = $request->input('cand_place_birth');
		$candidate->cand_place_issue = $request->input('cand_place_issue');
		$candidate->cand_date_issue = $request->input('cand_date_issue');
		$candidate->cand_date_expiry = $request->input('cand_date_expiry');
		$candidate->cand_dob = $request->input('cand_dob');
		$candidate->cand_gender = $request->input('cand_gender');
		$candidate->rel_cst_id = $request->input('rel_cst_id');
		$candidate->prof_id = $request->input('prof_id');
		$candidate->cand_pre_address = $request->input('cand_pre_address');
		$candidate->pincode = $request->input('pincode') ? $request->input('pincode') : '';
		$candidate->cand_photo = implode(',', $candidate_photo);
		$candidate->cand_passport_doc = implode(',', $candidate_passport);
		$candidate->cand_cv = implode(',', $candidate_cv);
		$candidate->cand_doc = implode(',', $candidate_doc);
		// if($country){
		// 	$candidate->nat_id = $country->country_id;
		// }
		$candidate->nat_id = $request->nat_id;
		$candidate->cand_visa_type = 22;
		$candidate->user_id = Auth::user()->user_id;
		$candidate->user_type = Auth::user()->user_type;
		$candidate->save();

		$cand_last_id = $candidate->cand_id;

		// Update in Passport Owner Table
		$pass_own = new Passowner();
		$pass_own->cand_id = $cand_last_id;
		$pass_own->cand_pass_no = $request->input('cand_passport_no');
		$pass_own->pty_id = $request->input('pty_id');
		$pass_own->user_id = Auth::user()->user_id;
		$pass_own->save();

		// Updated In Care of ID
		$care_off  = new CareOf();
		$care_off->cand_id = $cand_last_id;
		$care_off->cand_pass_no = $request->input('cand_passport_no');
		$care_off->care_of_id = $request->input('care_of_id');
		$care_off->user_id = Auth::user()->user_id;
		$care_off->pty_id = $request->input('pty_id');
		$care_off->save();

		// Create Activity
		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_last_id;
		$timeline->subject = 'New Candidate Created';
		$timeline->time = date("h:i:sa");
		$timeline->user_id = Auth::user()->user_id;
		$timeline->status = 0;
		$timeline->save();

		// $cand_service_type = $request->input('cand_service_type');
		$cand_service_type = 0;
		if ($cand_service_type != '0') {
			$cand_service_t = implode(',', $cand_service_type);
			for ($i = 0; $i < count($cand_service_type); $i++){
				Service::where('ser_id', '=', $cand_service_type[$i])->first();
				$cat_name = Service::where('ser_id', '=', $cand_service_type[$i])->first();
				if ($cat_name != '') {
					$c_name[] = $cat_name->ser_cat;
					$cat_name = implode(',', $c_name);
				}
			}
		}else{	
			$c_name='';
			$cand_service_t='0';
			$cat_name ='';
		}

		$cnt_cand_ser = CandidateService::count();
		$cnt_id = ($cnt_cand_ser + 1);
		$cs_ser_id = 'QSI/' . date('ym') . '/' . $cnt_id;
		$cs_inv_id = 'QCINV/' . date('ym') . '/' . $cnt_id;
		$candidate_ser = new CandidateService;
		$candidate_ser->cand_id = $cand_last_id;
		$candidate_ser->cand_visa_type = 22;
		$candidate_ser->pty_id = $request->input('pty_id');
		$candidate_ser->cs_ser_id = $cs_ser_id;
		$candidate_ser->cand_passport_type = $request->input('cand_passport_type');
		$candidate_ser->cand_choose_type = $request->input('cand_choose_type');
		$candidate_ser->cand_service_type = $cand_service_t;
		$candidate_ser->cand_service_category = $cat_name;
		$candidate_ser->cand_service_status = 'A';
		$candidate_ser->invoice_id = $cs_inv_id;
		$candidate_ser->user_id = Auth::user()->user_id;
		$candidate_ser->save();

		// Insert Party Id and Last ID
		$cand_status = new CandidateStatus();
		$cand_status->cand_id = $cand_last_id;
		$cand_status->pty_id = $request->input('pty_id');
		$cand_status->active_status = 'New Document';
		if($request->input('cand_passport_type') == 'ECR'){
			$cand_status->active_sub_status = 'ECR';
		}
		if($request->input('cand_passport_type') == 'ECNR'){
			$cand_status->active_sub_status = 'ECNR';
		}
		$cand_status->save();

		// Main Status
		$finalStatus = new CandCompFinalStatus();

		// Insert Candidate New Statue
		$cand_nst = new ComplCandStatus();

		$cand_nst->cand_id = $cand_last_id;
		if($request->input('cand_passport_type') == 'ECR'){
			$cand_nst->c_status = "ECR";
			$cand_nst->ECR = 1;
			$cand_nst->ECNR = 0;

			$finalStatus->cand_id = $cand_last_id;
			$finalStatus->final_status = 'ECR';
			$finalStatus->save();
		}
        
		if($request->input('cand_passport_type') == 'ECNR'){
			$cand_nst->c_status = "ECNR";
			$cand_nst->ECR = 0;
			$cand_nst->ECNR = 1;

			$finalStatus->cand_id = $cand_last_id;
			$finalStatus->final_status = 'ECNR';
			$finalStatus->save();
		}
		$cand_nst->save();

		$service_status = new ServiceStatus;
		$service_status->cand_id = $cand_last_id;
		$service_status->cs_ser_id = $cs_ser_id;
		$service_status->user_id = Auth::user()->user_id;
		$service_status->cand_passport_no = $request->input('cand_passport_no');
		$service_status->save();

		$service_details = new ServiceDetails;
		$service_details->cand_id = $cand_last_id;
		$service_details->cs_ser_id = $cs_ser_id;
		$service_details->user_id = Auth::user()->user_id;
		$service_details->cand_passport_no = $request->input('cand_passport_no');
		$service_details->save();

		$service_pay_status = new ServicePaymentStatus;
		$service_pay_status->cand_id = $cand_last_id;
		$service_pay_status->cs_ser_id = $cs_ser_id;
		$service_pay_status->user_id = Auth::user()->user_id;
		$service_pay_status->cand_passport_no = $request->input('cand_passport_no');
		$service_pay_status->save();

		// Send Notification for both Party and CareOff
		// Get API
		$getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();
		// Get Party Details
		if($request->pty_id != 200){
			$party = Party::where('pty_id','=',$request->pty_id)->first();
			SendCandPassStatus::dispatch($candidate,$getAPI,$party)->onQueue('visaservice');
		
			// Send Candidtae added passport
			SendCandidateRecord::dispatch($candidate,$getAPI,$party)->onQueue('default');
		}


		// Get User Details
		if($request->care_of_id != 200){
			$careof = User::where('user_id','=',$request->care_of_id)->first();
			SendCandPassStatusC::dispatch($candidate,$getAPI,$careof)->onQueue('visaservice');
		}

		// Update Party ID in Courier Table
		$courier = Courier::where('pass_no','=',$request->cand_passport_no)->first();
		$courierCS = Courierclone::where('pass_no',$request->cand_passport_no)->get();

		if(isset($courier)){
			$courier->pty_id = $request->input('pty_id');
			$courier->save();
		}

		if(isset($courierCS)){
			foreach($courierCS as $courierC){
				$courierC->pty_id = $request->input('pty_id');
				$courierC->save();
			}
		}

		Session::flash('success', 'Candidate created successfully !');
		// return redirect('services/candidate/list');
		return redirect()->back();
	}

	public function candidate_passport_update(Request $request){
	
		// Update In Candidate Tabele
		$cand = Candidate::find($request->input('cand_id'));
		// dd($cand->care_of_id);
		// Insert Data When Party Id Change
		if($cand->pty_id != $request->input('pty_id')){
			$pass_own = new Passowner();
			$pass_own->cand_id = $request->input('cand_id');
			$pass_own->cand_pass_no = $request->input('cand_passport_no');;
			$pass_own->pty_id = $request->input('pty_id');
			$pass_own->user_id = Auth::user()->user_id;
			$pass_own->save();
		}
		
		// Care of ID Change then
		if($cand->care_of_id != $request->input('care_of_id')){
			$care_off = new CareOf();
			$care_off->cand_id = $request->input('cand_id');
			$care_off->cand_pass_no = $request->input('cand_passport_no');
			$care_off->care_of_id = $request->input('care_of_id');
			$care_off->user_id = Auth::user()->user_id;
			$care_off->pty_id = $request->input('pty_id');
			$care_off->save();
		}

		$cand->cand_passport_no = $request->input('cand_passport_no') ? $request->input('cand_passport_no') : '';
		$cand->cand_fname = $request->input('cand_fname') ? $request->input('cand_fname') : '';
		$cand->cand_mname = $request->input('cand_mname') ? $request->input('cand_mname') : '';
		$cand->cand_lname = $request->input('cand_lname') ? $request->input('cand_lname') : '';
		$cand->cand_place_birth = $request->input('cand_place_birth') ? $request->input('cand_place_birth') : '';
		$cand->cand_place_issue  = $request->input('cand_place_issue') ? $request->input('cand_place_issue') : '';
		$cand->cand_date_issue = $request->input('cand_date_issue') ? $request->input('cand_date_issue') : '';
		$cand->cand_dob = $request->input('cand_dob') ? $request->input('cand_dob') : '';
		$cand->cand_date_expiry = $request->input('cand_date_expiry') ? $request->input('cand_date_expiry') : '';
		$cand->nat_id = $request->input('nat_id') ? $request->input('nat_id') : '';
		$cand->cand_gender = $request->input('cand_gender') ? $request->input('cand_gender') : '';
		$cand->rel_cst_id = $request->input('rel_cst_id') ? $request->input('rel_cst_id') : '';
		$cand->cand_passport_type = $request->input('cand_passport_type') ? $request->input('cand_passport_type') : '';
		$cand->prof_id = $request->input('prof_id') ? $request->input('prof_id') : '';
		$cand->cand_pre_address = $request->input('cand_pre_address') ? $request->input('cand_pre_address') : '';
		$cand->pty_id = $request->input('pty_id') ? $request->input('pty_id') : '';
		$cand->pincode = $request->input('pincode') ? $request->input('pincode') : '';
		$cand->care_of_id = $request->input('care_of_id') ? $request->input('care_of_id') : '';
		
		// Update in Service Status Table
		$ser_status = ServiceStatus::where('cand_id','=',$request->input('cand_id'))->first();
		$ser_status->cand_passport_no = $request->input('cand_passport_no') ? $request->input('cand_passport_no') : '';
		// Update in Service Details Table
		$ser_details = ServiceDetails::where('cand_id','=',$request->input('cand_id'))->first();
		$ser_details->cand_passport_no = $request->input('cand_passport_no') ? $request->input('cand_passport_no') : '';
		// Update in Service Payment Table
		$ser_payment = ServicePaymentStatus::where('cand_id','=',$request->input('cand_id'))->first();
		$ser_payment->cand_passport_no = $request->input('cand_passport_no') ? $request->input('cand_passport_no') : '';

		// Update in Candidate Status
		$cand_status = CandidateStatus::where('cand_id','=',$request->input('cand_id'))->first();
		$cand_status->pty_id = $request->input('pty_id') ? $request->input('pty_id') : '';

		// Update in Candidate Service
		$ser_cand = CandidateService::where('cand_id','=',$request->input('cand_id'))->first();
		$ser_cand->pty_id = $request->input('pty_id') ? $request->input('pty_id') : '';
		$ser_cand->cand_passport_type = $request->input('cand_passport_type') ? $request->input('cand_passport_type') : '';

		// Insert Candidate New Statue
		$cand_nst = ComplCandStatus::where('cand_id','=',$request->input('cand_id'))->first();
		
		// Check ECR or ECNR
		$chk_ecnr = CandCompFinalStatus::where('final_status','=','ECNR')->where('cand_id','=',$request->input('cand_id'))->first();
		$chk_ecr = CandCompFinalStatus::where('final_status','=','ECR')->where('cand_id','=',$request->input('cand_id'))->first();
		$new_chk = new CandCompFinalStatus();

		if($request->input('cand_passport_type') == 'ECR'){
			$cand_nst->c_status = 'ECR';
			$cand_nst->ECR = 1;
			$cand_nst->ECNR = 0;

			$status = explode(",",$cand_nst->c_status);
			$rem_status = ["ECNR"];
			$new_array = array_diff($status,$rem_status);

			$cand_nst->c_status = implode(",",$new_array);
			$cand_nst->save();

			if(isset($chk_ecnr)){
				$chk_ecnr->delete();
			}
			if($chk_ecr == ''){
				$new_chk->cand_id = $request->input('cand_id');
				$new_chk->final_status = "ECR";
				$new_chk->save();
			}
		}
		if($request->input('cand_passport_type') == 'ECNR'){
			$cand_nst->c_status = 'ECNR';
			$cand_nst->ECNR = 1;
			$cand_nst->ECR = 0;

			$status = explode(",",$cand_nst->c_status);
			$rem_status = ["ECR"];
			$new_array = array_diff($status,$rem_status);

			$cand_nst->c_status = implode(",",$new_array);
			$cand_nst->save();

			if(isset($chk_ecr)){
				$chk_ecr->delete();
			}
			if($chk_ecnr == ''){
				$new_chk->cand_id = $request->input('cand_id');
				$new_chk->final_status = "ECNR";
				$new_chk->save();
			}
		}
		$cand_nst->save();
		// Update in database table
		$cand->update();
		$ser_status->update();
		$ser_details->update();
		$ser_payment->update();
		$cand_status->update();
		$ser_cand->update();

		Session::flash('success', 'Passport updated successfully !');
		return redirect()->back();
	}

	public function newdocument_update(Request $request){
		$cand = Candidate::find($request->input('cand_id'));
		$cand->cand_fname = $request->input('cand_fname') ? $request->input('cand_fname') : '';
		$cand->cand_mname = $request->input('cand_mname') ? $request->input('cand_mname') : '';
		$cand->cand_lname = $request->input('cand_lname') ? $request->input('cand_lname') : '';
		$cand->cand_place_birth = $request->input('cand_place_birth') ? $request->input('cand_place_birth') :'';
		$cand->cand_date_expiry = $request->input('cand_date_expiry') ? $request->input('cand_date_expiry') : '';
		$cand->nat_id = $request->input('nat_id') ? $request->input('nat_id') :'';
		$cand->rel_cst_id = $request->input('rel_cst_id') ? $request->input('rel_cst_id') :'';
		$cand->prof_id = $request->input('prof_id') ? $request->input('prof_id') :'';
		$cand->cand_passport_type = $request->input('cand_passport_type');
		$cand->pty_id = $request->input('pty_id') ? $request->input('pty_id') :'';
		$cand->cand_passport_no = $request->input('cand_passport_no') ? $request->input('cand_passport_no'): '';

		$cand->cand_pre_address = $request->input('cand_pre_address') ? $request->input('cand_pre_address') :'';
		$cand->cand_pass_status = $request->input('cand_pass_status') ? $request->input('cand_pass_status') :'';
		$cand->cand_gender = $request->input('cand_gender') ? $request->input('cand_gender') :'';
		$cand->cand_mobile_no = $request->input('cand_mobile_no') ? $request->input('cand_mobile_no') :'';
		$cand->save();

		Session::flash('success', 'Candidate updated successfully !');
		return redirect('services/candidate-status/'.$request->input('cand_id'));
	}

	public function get_medicle_info(Request $request){
		$med_id = $request->input('med_id');
		$m = Medicle::where('med_id','=',$med_id)->first();
		?>
		<table class="card-datatable table-responsive" style="width: ">
			<tr><th>Centre</th><th>Email</th><th>Phone</th><th>City</th></tr>
			<tr><td><?php echo $m->med_center_name?></td>
				<td><?php echo $m->med_email?></td>
				<td><?php echo $m->med_mobile_no?></td>
				<td><?php echo $m->med_city?></td>
			</tr>
		</table>
		<?php
	}

	public function candidate_status($id) {
		global $done ;
		// Globally Declare Variable
		$cand_info = DB::table('qr_candidate_tbl as cand')
			->leftjoin('qr_services_details as ser_det','ser_det.cand_id','=','cand.cand_id')
			->leftjoin('qr_party_tbl as pty','pty.pty_id','=','cand.pty_id')
			->leftjoin('qr_profession_tbl as prof','prof.prof_id','=','cand.prof_id')
			->leftjoin('qr_nationality_tbl as nat','nat.nat_id','=','cand.nat_id')
			->leftjoin('qr_religion_cast_tbl as rel','rel.rel_cst_id','=','cand.rel_cst_id')
			->leftjoin('qr_employee_candidate as emp_cand','emp_cand.cand_id','=','cand.cand_id')
		->leftjoin('qr_employee_tbl as emp_data','emp_data.pty_id','=','cand.pty_id')
		// ->leftjoin('qr_employee_tbl as emp_data','emp_data.emp_id','=','emp_cand.emp_id')
		->leftjoin('qr_candidate_place_issue as cand_place','cand_place.id','=','cand.cand_place_issue')
		->leftjoin('qr_candidate_status_tbl as cand_status','cand_status.cand_id','=','cand.cand_id')
		->leftjoin('qr_services_status as ser_status','cand.cand_id','=','ser_status.cand_id')
		->select('cand.*','ser_det.*','cand_status.*','rel.rel_cst_name as religion','nat.nat_name as nationality','prof.prof_eng_name as profession','emp_cand.cand_id as emp_ass_chk','pty.pty_full_name as party_name','pty.pty_ag_name as agency_name','emp_data.emp_visa_no','emp_data.spon_nm_eng','emp_data.spon_nm_arab','emp_data.profession as emp_profession','emp_data.emp_id_no','emp_data.emp_id','cand_place.place')
		->where('cand.cand_id','=',$id)
		->first();
		// dd($cand_info);
		$pass_data = Candidate::where('cand_id', '=', $id)->first();
		$pass_no = $pass_data->cand_passport_no;
		$cand_ser = CandidateService::where('cand_id', $id)->first();
		if($cand_ser){
			$cand_cat = explode(",", $cand_ser->cand_service_category);
		}
		$party_data = Party::where('pty_id', $pass_data->pty_id)->first();		
		// $mofa_d = ServiceDetails::where('cand_passport_no', $pass_no)->first();
		$mofa_d = ServiceDetails::where('cand_id', $id)->first();
		if($mofa_d){
			$mofa_data = unserialize($mofa_d->mofa);
		}	
		if (isset($pass_data) && isset($cand_ser) && isset($mofa_data)) {
			$return_arr['cand_fname'] = $pass_data->cand_fname;
			$return_arr['pty_fname'] = $party_data->pty_ag_name;
			$return_arr['image'] = $pass_data->image;
			$return_arr['cand_id'] = $pass_data->cand_id;
			$return_arr['cand_cat'] = $cand_ser->cand_service_category;
			if($mofa_data){
				$return_arr['mofa_no'] = $mofa_data['mofa_no'];	
			}
			$return_arr['pass_no'] = $pass_data->cand_passport_no;
			$st_data = ServiceStatus::where('cand_id', $id)->first();
			if ($st_data->cancel == '1' || $st_data->work_done == '1') {
				$done='1';
			}
			if(in_array('emig', $cand_cat) && in_array('air_ticket', $cand_cat)){
				if($st_data->emi_done == '1' && $st_data->flight_book == '1'){
					$done='1';
				} 
			}else if($st_data->emi_done == '1' && !in_array('air_ticket', $cand_cat)){
				$done='1';
			}else if($st_data->flight_book == '1'){
				$done='1';
			}else if(!in_array('emig', $cand_cat) && !in_array('air_ticket', $cand_cat) && ($cand_ser->cand_visa_type)){
				$visa='1';
				if($visa==1 && $st_data->submitted == '1' && $st_data->visa_cancel == '0' && $st_data->visa_canceled_final == '0'){
					$done='1';
				}
			}

			if($done=='1'){
				$return_arr['status'] = 'Completed';
				$title = 'Completed';
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);
				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}
				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/completed', ['pageConfigs' => $pageConfigs,'return_arr' => $return_arr], ['cand_info' => $cand_info]);
			}

			if($st_data->visa_canceled_final == '1' && $st_data->visa_cancel == '1' && $st_data->submitted == '1'){
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);
				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}
				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/visa_canceled', [ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'ser_info' => $ser_info,'cand_info' => $cand_info]);
			}

			if($st_data->visa_cancel == '1' && $st_data->submitted == '1' && $st_data->visa_canceled_final == '0'){
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);
				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}
				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/visa_can', [ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'ser_info' => $ser_info,'cand_info' => $cand_info]);
			}

			if ($st_data->flight_book == '0' && $st_data->flight_book_requied == '1' && $st_data->rejected == '0' && $st_data->submitted == '1' && $st_data->hold == '0' && $st_data->submission == '1' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_copy == '0' && $st_data->mofa == '1' && in_array("air_ticket", $cand_cat)) {
				$return_arr['status'] = 'air ticket';
				$title = 'air ticket';
			
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);

				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}

				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}

				// $cand_info->mofa = $new_doc['photo'];
				// $cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/air_ticket', ['pageConfigs' => $pageConfigs,'return_arr' => $return_arr], ['cand_info' => $cand_info]);

			}
			if ($st_data->emi_reject == '1' &&  $st_data->emi_done == '0' && $st_data->emi_reject_next == '0' && $st_data->emi_apply == '1' && $st_data->rejected == '0' && $st_data->submitted == '1' && $st_data->hold == '0' && $st_data->submission == '1' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1' && in_array("emig", $cand_cat)) {
				$return_arr['status'] = ' emigartion reject';
				$title = 'in emigartion';
			
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);

				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}

				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}
				
				// $cand_info->mofa = $new_doc['photo'];
				// $cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/emigration_rejected', ['pageConfigs' => $pageConfigs,'return_arr' => $return_arr], ['cand_info' => $cand_info]);
			}

			if ($st_data->emi_done == '0' && $st_data->emi_apply == '1' && $st_data->rejected == '0' && $st_data->emi_reject_next == '0' && $st_data->submitted == '1' && $st_data->hold == '0' && $st_data->submission == '1' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1' && in_array("emig", $cand_cat)) {

				$return_arr['status'] = 'in emigartion';
				$title = 'in emigartion';
			
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);

				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}

				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}
			
				// $cand_info->mofa = $new_doc['photo'];
				// $cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/in_emigration', ['pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info]);

			}

			if ($st_data->emi_apply == '0'  && $st_data->rejected == '0' && $st_data->submitted == '1' && $st_data->hold == '0' && $st_data->submission == '1' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1' && in_array("emig", $cand_cat)) {
				$return_arr['status'] = 'for emigartion';
				$title = 'for emigartion';
								
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);

				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}

				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}

				// $cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/for_emigration', ['pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info]);
			}

			if ($st_data->rejected == '1' && $st_data->reject_save_next == '0') {
				$return_arr['status'] = 'rejected';
				$title = 'rejected';
			
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);
				$submitted_data = unserialize($ser_info->submitted);

				

				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}

				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}
				
				// $cand_info->mofa = $new_doc['photo'];
				// $cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/rejected', [ 'pageConfigs' => $pageConfigs,'submitted_data' => $submitted_data,'ser_info' => $ser_info,'return_arr' => $return_arr,'cand_info' => $cand_info]);
			}

			if ($st_data->submitted == '0' && $st_data->submission == '1'  && $st_data->hold == '0'  && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1') {

				$return_arr['status'] = 'Submitted';
				$title = 'Submitted';
				// $cand_info = Candidate::find($id);
				$ser_info = Servicedetails::where('cand_id', $id)->first();

				// Get EmpID from Employee Candidate
				$getEmpID = EmployeeCandidate::where('cand_id','=',$id)->first();
				$empIDs = Employee::where('emp_id','=',$getEmpID->emp_id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);
				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}
				
				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}

				// $cand_info->mofa = $new_doc['photo'];
				// $cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/submitted', [ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info,'empIDs' => $empIDs]);
			}

			if ($st_data->hold == '1') {
				$return_arr['status'] = 'Hold';
				$title = 'Hold';
			
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);
			
				$cand_info->mofa = $new_doc['photo'];
				$cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/hold', [ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info]);
			}

			// && $st_data->ready_for_documentation == '1'

			// if ($st_data->submission == '0' && $st_data->ready_for_documentation == '1' && $st_data->hold == '0' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' &&  $st_data->ass_candidate_to_employer == '1' && $st_data->mofa == '1' ) {
			// 	$title = 'Ready for submission';
			// 	$ser_info = Servicedetails::where('cand_id', $id)->first();
			// 	$new_doc = unserialize($ser_info->new_document);
			// 	$mofa_data = unserialize($ser_info->mofa);
				
			// 	if($cand_info->mofa !=''){
			// 		$cand_info->mofa = $new_doc['photo'];
			// 	}

			// 	if($mofa_data == true){
			// 		$cand_info->mofa_no = $mofa_data['mofa_no'];
			// 	}else{
			// 		$cand_info->mofa_no = '';
			// 	}

			// 	$pageConfigs = ['pageHeader' => false];
			// 	return view('/content/service/ready_for_submission', [ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info,'ser_info' => $ser_info]);
			// }

			// && $st_data->ready_for_documentation == '0'
			if($st_data->submission == '0' && $st_data->hold == '0' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' &&  $st_data->ass_candidate_to_employer == '1' && $st_data->mofa == '1'){
				$title = 'Ready for documentation';
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);
				
				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}

				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}
				
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/ready_for_documentation',[ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info,'ser_info' => $ser_info]);
			}
			
			if ($st_data->submission == '0' && $st_data->hold == '0' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->musaned_del == '1' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->ass_candidate_to_employer == '0' && $st_data->mofa == '1' ) {
				$title = 'Assign candidate to employer';
				//New Document
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				// $new_doc = unserialize($cand_info->new_document);
				$mofa_data = unserialize($cand_info->mofa);

				// Emp Cand Data
				$cand_emp_d = EmployeeCandidate::where('cand_id','=',$id)->first();

				if($cand_info->mofa !=''){
					$cand_info->mofa = $new_doc['photo'];
				}

				if($mofa_data == true){
					$cand_info->mofa_no = $mofa_data['mofa_no'];
				}else{
					$cand_info->mofa_no = '';
				}

				// $cand_info->mofa = $new_doc['photo'];
				// $cand_info->mofa_no = $mofa_data['mofa_no'];
			

				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/ass_cand_to_emp', [ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info,'emp_ass_chk' => $cand_info->emp_ass_chk,'cand_emp_d' => $cand_emp_d]);
			}

			if ($st_data->awaited_passport == '0' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->musaned_del == '1' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1') {

				$title = 'Awaited passport';
			
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
          
				$cand_info->mofa = $new_doc['photo'];
				$cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/awaited_passport', [ 'pageConfigs' => $pageConfigs,'ser_info' => $ser_info,'return_arr' => $return_arr,'cand_info' => $cand_info]);
			}

			if ($st_data->medicle_online_update == '1' && $st_data->medicle_online_required == '1' && $st_data->medicle == '1' && $st_data->musaned_del == '1' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1') {

				$title = 'Medicle online update';
				$cand_info = Candidate::find($id);
                // dd($cand_info);
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
                // dd($new_doc['photo']);
				$party = Party::where('pty_id', $cand_info->pty_id)->first();
				$profession = Profession::where('prof_id', $cand_info->prof_id)->first();
				$nationality = Nationality::where('nat_id', $cand_info->nat_id)->first();
				$religion = ReligionCast::where('rel_cst_id', $cand_info->rel_cst_id)->first();
				$mofa_data = unserialize($ser_info->mofa);
				$cand_info->religion = $religion->rel_cst_name;
				if($nationality)
					$cand_info->nationality = $nationality->nat_name;
				$cand_info->profession = $profession->prof_eng_name;
				$cand_info->party_name = $party->pty_full_name;
				$cand_info->agency_name = $party->pty_ag_name;
				$cand_info->pty_fname = $party->pty_full_name;
				$cand_info->pty_lname = $party->pty_lname;
				$cand_info->mofa = $new_doc['photo'];
				$cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/medicle_online_update', [ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info]);
			}
			if ($st_data->medicle == '0' && $st_data->musaned_del == '1' && $st_data->mofa_paid == '1'  && $st_data->passport_original == '1'  && $st_data->mofa == '1') {

				$title = 'Medicle';
				$cand_info = Candidate::find($id);
				$med_info = Medicle::all();

				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);
        
          		$med_data = unserialize($ser_info->medicle);
      
				$party = Party::where('pty_id', $cand_info->pty_id)->first();
				$profession = Profession::where('prof_id', $cand_info->prof_id)->first();
				$nationality = Nationality::where('nat_id', $cand_info->nat_id)->first();
				$religion = ReligionCast::where('rel_cst_id', $cand_info->rel_cst_id)->first();
				$cand_info->religion = $religion->rel_cst_name;
				if($nationality)
				$cand_info->nationality = $nationality->nat_name;
				$cand_info->profession = $profession->prof_eng_name;
				$cand_info->party_name = $party->pty_fname;
				$cand_info->agency_name = $party->pty_ag_name;
				$cand_info->pty_fname = $party->pty_full_name;
				$cand_info->pty_lname = $party->pty_lname;
				$cand_info->mofa = $new_doc['photo'];
				$cand_info->mofa_no = $mofa_data['mofa_no'];
				$cand_info->med_info =$med_info;
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/medicle', [ 'pageConfigs' => $pageConfigs,
					'return_arr' => $return_arr,
					'cand_info' => $cand_info,
					'med_data' => $med_data,
					'ser_info' => $ser_info	
				]);
			}

			if($st_data->musaned_del == '0' && $st_data->mofa == '1' && $st_data->mofa_paid == '1' && $st_data->musaned == '1' && $st_data->new_document == '1'){
				$return_arr['status'] = 'musaned_del';
				$title = 'Musaned Delete';
				$party = Party::where('pty_id', $cand_info->pty_id)->first();
				$profession = Profession::where('prof_id', $cand_info->prof_id)->first();
				$nationality = Nationality::where('nat_id', $cand_info->nat_id)->first();
				$religion = ReligionCast::where('rel_cst_id', $cand_info->rel_cst_id)->first();
				$cand_info->religion = $religion->rel_cst_name;
				$ser_info = ServiceDetails::where('cand_id','=',$cand_info->cand_id)->first();
				if($nationality)
					$cand_info->nationality = $nationality->nat_name;
					$cand_info->profession = $profession->prof_eng_name;
					$cand_info->party_name = $party->pty_full_name;
					$cand_info->agency_name = $party->pty_ag_name;
					$cand_info->pty_fname = $party->pty_fname;
					$cand_info->pty_lname = $party->pty_lname;
					$pageConfigs = ['pageHeader' => false];






				return view('/content/service/musaned_del', ['return_arr' => $return_arr,'cand_info' => $cand_info,'pageConfigs' => $pageConfigs,'ser_info' => $ser_info]);
			}

			if (($st_data->mofa == '1' && $st_data->mofa_unpaid == '1' && $st_data->musaned == '1' && $st_data->new_document == '1')) {
				$title = 'Mofa unpaid';

				$mofa_details = DB::table('qr_mofa_price as mfp')
				->leftjoin('qr_mofa_payment_category as mfpc','mfpc.id','=','mfp.pay_cat')
				->select('mfpc.*')
				->where('mfp.ser_id','=',$cand_info->cand_visa_type)
				->get();

				// Service Details
				$ser_info = Servicedetails::where('cand_id', $cand_info->cand_id)->first();
				$new_doc = unserialize($ser_info->new_document);
				
				// $new_doc = unserialize($cand_info->new_document);
				
				

				$mofa_data = unserialize($cand_info->mofa);

				$cand_info->mofa = $new_doc['photo'];
				$cand_info->mofa_no = $mofa_data['mofa_no'];
				
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/mofa_unpaid', [ 'pageConfigs' => $pageConfigs,'return_arr' =>
					$return_arr,'cand_info' => $cand_info,'mofa_details' => $mofa_details,'mofa_data' => $mofa_data]);
			}

			if (($st_data->mofa == '1' && $st_data->passport_copy == '1' && $st_data->musaned == '1' && $st_data->new_document == '1')) {
				$title = 'Passport copy';
				$cand_info = Candidate::find($id);
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$mofa_data = unserialize($ser_info->mofa);
				$party = Party::where('pty_id', $cand_info->pty_id)->first();
				$profession = Profession::where('prof_id', $cand_info->prof_id)->first();
				$nationality = Nationality::where('nat_id', $cand_info->nat_id)->first();
				$religion = ReligionCast::where('rel_cst_id', $cand_info->rel_cst_id)->first();
				$cand_info->religion = $religion->rel_cst_name;
				if($nationality)
					$cand_info->nationality = $nationality->nat_name;
				$cand_info->profession = $profession->prof_eng_name;
				$cand_info->party_name = $party->pty_full_name;
				$cand_info->agency_name = $party->pty_ag_name;

				
				$cand_info->mofa = $new_doc['photo'];
				$cand_info->mofa_no = $mofa_data['mofa_no'];
				$pageConfigs = ['pageHeader' => false];
				return view('/content/service/passport_copy', [ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info]);
			}

			if (($st_data->mofa == '0' && $st_data->musaned == '1' && $st_data->new_document == '1')) {

				$title = 'Mofa';
				$cand_info = Candidate::find($id);
				$ser_info = Servicedetails::where('cand_id', $id)->first();
				$new_doc = unserialize($ser_info->new_document);
				$party = Party::where('pty_id', $cand_info->pty_id)->first();
				$profession = Profession::where('prof_id', $cand_info->prof_id)->first();
				$nationality = Nationality::where('nat_id', $cand_info->nat_id)->first();
				$religion = ReligionCast::where('rel_cst_id', $cand_info->rel_cst_id)->first();
				$cand_info->religion = $religion->rel_cst_name;
				if($nationality)
					$cand_info->nationality = $nationality->nat_name;
				$cand_info->profession = $profession->prof_eng_name;
				$cand_info->party_name = $party->pty_fname;
				$cand_info->agency_name = $party->pty_ag_name;
				$cand_info->pty_fname = $party->pty_fname;
				$cand_info->pty_lname = $party->pty_lname;
				$cand_info->mofa = $new_doc['photo'];
				$pageConfigs = ['pageHeader' => false];

				return view('/content/service/mofa', [ 'pageConfigs' => $pageConfigs,'return_arr' => $return_arr,'cand_info' => $cand_info]);
			}

			if(($st_data->musaned == '0' && $st_data->new_document == '1')){
				$return_arr['status'] = 'musaned';
				$title = 'Musaned';
				// $cand_info = Candidate::find($id);
				$party = Party::where('pty_id', $cand_info->pty_id)->first();
				$profession = Profession::where('prof_id', $cand_info->prof_id)->first();
				$nationality = Nationality::where('nat_id', $cand_info->nat_id)->first();
				$religion = ReligionCast::where('rel_cst_id', $cand_info->rel_cst_id)->first();
				$ser_info = Servicedetails::where('cand_id', $cand_info->cand_id)->first();
				$cand_info->religion = $religion->rel_cst_name;
				if($nationality)
					$cand_info->nationality = $nationality->nat_name;
					$cand_info->profession = $profession->prof_eng_name;
					$cand_info->party_name = $party->pty_full_name;
					$cand_info->agency_name = $party->pty_ag_name;
					$cand_info->pty_fname = $party->pty_fname;
					$cand_info->pty_lname = $party->pty_lname;
					$pageConfigs = ['pageHeader' => false];




				return view('/content/service/musaned', ['return_arr' => $return_arr,'cand_info' => $cand_info,'ser_info' => $ser_info,'pageConfigs' => $pageConfigs]);

			}
			
			if (($st_data->new_document == '0')) {

				$return_arr['status'] = 'new_document';
				$title = 'New document';
				$cand_info = Candidate::find($id);
				$party = Party::where('pty_id', $cand_info->pty_id)->first();
				$profession = Profession::where('prof_id', $cand_info->prof_id)->first();
				$nationality = Nationality::where('nat_id', $cand_info->nat_id)->first();
				$religion = ReligionCast::where('rel_cst_id', $cand_info->rel_cst_id)->first();
				$cand_info->religion = $religion->rel_cst_name;
				if($nationality)
					$cand_info->nationality = $nationality->nat_name;
					$cand_info->profession = $profession->prof_eng_name;
					$cand_info->party_name = $party->pty_full_name;
					$cand_info->agency_name = $party->pty_ag_name;
					$cand_info->pty_fname = $party->pty_fname;
					$cand_info->pty_lname = $party->pty_lname;
					$pageConfigs = ['pageHeader' => false];
         			// dd($cand_info);
					return view('/content/service/new-document', ['return_arr' => $return_arr,'cand_info' => $cand_info,'pageConfigs' => $pageConfigs]);
			}

			if (!isset($cand_cat)) {
				$return_arr['status'] = 'passport';
			}
            // echo json_encode($return_arr);
		} else {
			$return_arr['empty'] = '1';
			echo json_encode($return_arr);
		}
	}

	public function updatedocuquality(Request $request){

		$ser_det = ServiceDetails::where('cand_id','=',$request->id)->first();
		$ser_det->rsqimg = $request->rsqimg;
		$ser_det->save();

		
	}

	public function updatedocphotoqt(Request $request){
		$ser_det = ServiceDetails::where('cand_id','=',$request->id)->first();
		$ser_det->rsphoto_st = true;
		$ser_det->save();
	}

	public function updatedocpassportqt(Request $request){
		
		$ser_det = ServiceDetails::where('cand_id','=',$request->id)->first();
		$ser_det->rspassport_st = true;
		$ser_det->save();

	}

	public function medicle_tab_list($id)
	{
		$cand_info = Candidate::find($id);

		$pageConfigs = ['pageHeader' => false];
		return view('content/service/medicle-tab', [ 'pageConfigs' => $pageConfigs,'cand_info' => $cand_info]);
	}

	public function new_document_store(Request $request) {
		
		$cand_id = $request->input('cand_id');
		$cand_data = Candidate::find($cand_id);

		$cand_photo = $cand_data->cand_photo;
		$cand_doc = $cand_data->cand_doc;
		$cand_pass = $cand_data->cand_passport_doc;
		

		if ($request->hasFile('cand_photo')) {
			$file = $request->file('cand_photo');
			$file_count = File::files(base_path().'/public/image/service-candidate');
			$filecount = 0;

			if ($file_count !== false) {
				$filecount = count($file_count);
			}
			$file_exe = $file->getClientOriginalExtension();
			$name = $filecount . '.' . $file_exe;

			$file->move(base_path().'/public/image/service-candidate', $name);
			// $file->move(base_path().'/public/image/service-candidate', $name);
			$candidate_photo = $name;

		} else {
			$candidate_photo = $cand_photo;
		}

		// if ($request->hasFile('cand_doc')) {
		// 	$files = $request->file('cand_doc');
		// 	foreach ($files as $file) {
		// 		$file_count = File::files(base_path().'/public/image/service-candidate');
		// 		$filecount = 0;

		// 		if ($file_count !== false) {
		// 			$filecount = count($file_count);
		// 		}
		// 		$file_exe = $file->getClientOriginalExtension();
		// 		$name = $filecount . '.' . $file_exe;
		// 		$file->move(base_path().'/public/image/service-candidate', $name);
				
		// 		$candidate_doc_array[] = $name;

		// 	}
		// } else {

		// 	$candidate_doc = $cand_doc;
			
		// }


		// upoload candidate passport
		if($request->hasFile('cand_passport_doc')){
			$file = $request->file('cand_passport_doc');
			$file_count = File::files(base_path().'/public/image/service-candidate');
			$filecount = 0;
			if ($file_count !== false) {
				$filecount = count($file_count);
			}
			$file_exe = $file->getClientOriginalExtension();
			$name = $filecount . '.' . $file_exe;
			$file->move(base_path().'/public/image/service-candidate', $name);
			$candidate_pass = $name;
		}else{
			$candidate_pass = $cand_pass;
		}

		
		
		// hide kalam start**
		// $up_date = Candidate::where('cand_id','=',$cand_id)->first();
		// $up_date->cand_photo = $candidate_photo;
		// $up_date->cand_passport_doc = $candidate_pass;
		// hide kalam end**

		// Changes By Kalam
		// if(isset($candidate_doc_array)){
		// 	$up_date->cand_doc = implode(",",$candidate_doc_array);
		// }else{
		// 	$up_date->cand_doc = $candidate_doc;
		// }
		
		// if(isset($cand_doc_array)){
		// $up_date->cand_doc = implode(",",$cand_doc_array);
		// }else{
		// 	$up_date->cand_doc = $candidate_doc;
		// }
		
		// hide kalam end**
		// $up_date->update();

		$photo = $candidate_photo;
		$pass_doc = $candidate_pass;
		// Change By Kalam
		// if(isset($candidate_doc_array)){
		// 	$doc = implode(',', $candidate_doc_array);
		// }else{
		// 	$doc = $candidate_doc;
		// }


		// if(isset($cand_doc_array)){
		// $doc = implode(',', $cand_doc_array);	
		// }else{
		// 	$doc = $candidate_doc;
		// }
		

		$pass_data = array(
			'photo' => $photo,
			'doc' => $pass_doc,
			'created_at' =>  Carbon::now()->toDateTimeString()

		);
	
		

		// change By Kalam
		if($photo && $pass_doc){
			$pass_data = serialize($pass_data);
			$pass_no = $request->input('passport');
			// $sd = ServiceDetails::where('cand_passport_no', '=', $pass_no)->first();
			$sd = ServiceDetails::where('cand_id', '=', $request->input('cand_id'))->first();
			$sd->new_document = $pass_data;
			$sd->musaned_photo = $pass_doc;
			$sd->musaned_required  = $request->input('musaned_required');
			$sd->pass_dc_q  = $request->input('pass_dc_q');
			$sd->pass_dc_q_id  = Auth::user()->user_id;
			// $sd->musaned_user_by = Auth::user()->user_id;

			$sd->save();

			// create variable 

			$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
			$cand_status->newdocument = 1;

			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $request->input('cand_id'))->first();
			$ser_status->new_document = 1;

			// Update in Candidate Status New
			// $cand_st_new = CandStatusNew::where('cand_id','=',$cand_id)->first();

			// Update in Stage and Status
			// $stage_f = CandStageF::where('cand_id','=',$cand_id)->first();
			// $status_f = CandCstatusF::where('cand_id','=',$cand_id)->first();
			// $incom_f = CandIncomF::where('cand_id','=',$cand_id)->first();

			if ($request->input('musaned_required') == 'musaned_not_req') {
				$ser_status->musaned = 1;
				$ser_status->musaned_del = 1;

				$cand_status->waiting_for_musaned_registration = 1;
				$cand_status->active_status = 'Waiting For Mofa Generate';

				$cand_status->incomplete_status = '';

				// Update in new Candidate Status
				// $cand_st_new->stage = 'Waiting For Mofa Generate';

				// $stage_f->waiting_for_mofa = 1;
				// $stage_f->new_document = 0;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// Update Timeline
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Musaned Not Required';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 1;
				$timeline->save();


				
			}

			if($request->input('musaned_required') == 'musaned_req'){

				$cand_status->active_status = 'Waiting For Musaned Registration';
				$cand_status->incomplete_status = '';
				// Update in new Candidate Status
				// $cand_st_new->stage = 'Waiting For Musaned Registration';


				// Update Timeline
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Musaned Required';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 1;
				$timeline->save();

				// $stage_f->waiting_for_musaned_reg = 1;
				// $stage_f->new_document = 0;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

			}

			$ser_status->save();
			$cand_status->save();
			// $cand_st_new->save();
			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject = 'New Document Created';
			$timeline->time = date("h:i:sa");
			$timeline->user_id = Auth::user()->user_id;
			$timeline->status = 1;
			$timeline->save();

			Session::flash('success', 'New document created successfully !');
			return redirect('services/candidate-status/' . $cand_id);

		}else{
			Session::flash('error', 'Please select photo and document !');
			return redirect('services/candidate-status/' . $cand_id);
		}

		// if (($doc) && ($photo)) {

		// 	$pass_data = serialize($pass_data);
		// 	$pass_no = $request->input('passport');
		// 	$sd = ServiceDetails::where('cand_passport_no', '=', $pass_no)->first();
		// 	$sd->new_document = $pass_data;
		// 	$sd->musaned_required  = $request->input('musaned_required');
		// 	$sd->musaned_user_by = Auth::user()->user_id;

		// 	$sd->save();

		// 	// create variable 

		// 	$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		// 	$cand_status->newdocument = 1;


		// 	$ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
		// 	$ser_status->new_document = 1;
		// 	if ($request->input('musaned_required') == 'musaned_not_req') {
		// 		$ser_status->musaned = 1;
		// 		$ser_status->musaned_del = 1;

		// 		$cand_status->waiting_for_musaned_registration = 1;
		// 		$cand_status->active_status = 'Waiting For Mofa Generate';
		// 		// Update Timeline
		// 		date_default_timezone_set('Asia/Kolkata');
		// 		$timeline = new Timeline();
		// 		$timeline->cand_id = $cand_id;
		// 		$timeline->subject = 'Musaned Not Required';
		// 		$timeline->time = date("h:i:sa");
		// 		$timeline->user_id = Auth::user()->user_id;
		// 		$timeline->status = 1;
		// 		$timeline->save();


				
		// 	}

		// 	if($request->input('musaned_required') == 'musaned_req'){

		// 		$cand_status->active_status = 'Waiting For Musaned Registration';
		// 		// Update Timeline
		// 		date_default_timezone_set('Asia/Kolkata');
		// 		$timeline = new Timeline();
		// 		$timeline->cand_id = $cand_id;
		// 		$timeline->subject = 'Musaned Required';
		// 		$timeline->time = date("h:i:sa");
		// 		$timeline->user_id = Auth::user()->user_id;
		// 		$timeline->status = 1;
		// 		$timeline->save();
		// 	}

		// 	$ser_status->save();
		// 	$cand_status->save();
		// 	date_default_timezone_set('Asia/Kolkata');
		// 	$timeline = new Timeline();
		// 	$timeline->cand_id = $cand_id;
		// 	$timeline->subject = 'New Document Created';
		// 	$timeline->time = date("h:i:sa");
		// 	$timeline->user_id = Auth::user()->user_id;
		// 	$timeline->status = 1;
		// 	$timeline->save();

		// 	Session::flash('success', 'New document created successfully !');
		// 	return redirect('services/candidate-status/' . $cand_id);

		// } else {
		// 	Session::flash('error', 'Please select photo and document !');
		// 	return redirect('services/candidate-status/' . $cand_id);
		// }

		

	}

	public function musaned_store(Request $request)
	{
		$cand_id = $request->input('cand_id');
		$pass_no = $request->input('passport');
		$musaned_registered = $request->input('musaned_registered');
		$musaned_date = $request->input('musaned_date');
		$add_another_office = $request->input('add_another_office');
		
		// Candidate Status
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		$can_status = array($cand_status->active_sub_status,"Musaned Registered");

		// Service Details
		$ser_d = ServiceDetails::where('cand_id','=',$cand_id)->first();
		
		// Service Status
		$ser_s = ServiceStatus::where('cand_id','=',$cand_id)->first();
		
		// Candidate Details
		$cand_sd = Candidate::where('cand_id','=',$cand_id)->first();
		$pty_id = $cand_sd->pty_id;
		$care_of_id = $cand_sd->care_of_id;
		$cand_name = $cand_sd->cand_fname.' '.$cand_sd->cand_mname.' '.$cand_sd->cand_lname;
		$party = Party::where('pty_id','=',$pty_id)->first();
		$user = User::where('user_id','=',$care_of_id)->first();
		
		// Get Service API
		$getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();

		// Update in Candidate Status New
		// $cand_st_new = CandStatusNew::where('cand_id','=',$cand_id)->first();

		$cand_nst = ComplCandStatus::where('cand_id','=',$cand_id)->first();
		$cs_status = array($cand_nst->c_status,"Musaned Registered");

		// Main Status
		$finalStatus = new CandCompFinalStatus();

		// dd($cs_status);
		if ($musaned_registered != '') {
			if ($musaned_registered == 'registered') {

				$ser_d->musaned_registered = $musaned_registered;
				$ser_d->musaned_date = $musaned_date;
				$ser_d->musaned_user_by = Auth::user()->user_id;
	
				if($request->has('musaned_doc')){
					$file = $request->file('musaned_doc');
					$file_count = File::files(base_path().'/public/image/service-candidate');
					$filecount = 0;
					if ($file_count !== false) {
						$filecount = count($file_count);
					}
					$file_exe = $file->getClientOriginalExtension();
					$name = $filecount . '.' . $file_exe;
					$file->move(base_path().'/public/image/service-candidate', $name);
					$candidate_musaned = $name;
	
					$ser_d->musaned_doc = $candidate_musaned;
				}
				
				// Update in Service Status
				$ser_s->musaned = 1;
				$ser_s->musaned_del = 1;
				$ser_s->save();

				// Candidate Status Upate
				$cand_status->musaned_registered = 1;
				$cand_status->musaned_rejection = 0;
				$cand_status->active_status = 'Waiting For Mofa Generate';
				$cand_status->active_sub_status = implode(",",$can_status);
				$cand_status->incomplete_status = '';
				$cand_status->save();
				
				// Final Status Create
				// check Musaned Registered exist
				$chk_mus = CandCompFinalStatus::where('final_status','=','Musaned Registered')->where('cand_id','=',$cand_id)->first();
				if($chk_mus == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = 'Musaned Registered';
					$finalStatus->save();
				}

				// New Candidate Status
				$cand_nst->cand_id = $request->input('cand_id');
				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->MUSANED_REGISTERD = 1;
				$cand_nst->save();

				// Update in Activities
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Musaned Registered';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 1;
				$timeline->save();

				// Get Details for Send Mail and Whatsapp

			
				/* Temporary Hide Start */
				if (isset($party)) {
					$party_name = $party->pty_full_name;
					$party_agency = $party->pty_ag_name;
					if($party->pty_id == '200'){
						// Primary Contact
						$party_email = '';
						$party_contact = '';
						// Secondary Details
						$party_email2 = '';
						$party_contact2 = '';
					}else{
						// Primary Contact
						$party_email = $party->pty_email;
						$party_contact = '91'.$party->pty_comp_contact;
						// Secondary Details
						$party_email2 = $party->sec_email;
						$party_contact2 = '91'.$party->pty_contact_no;
					}


					// Mail Data
					$mesSub = 'Musaned Registered Successfully for '.$cand_name.' with '.$pass_no;
					$file_path = public_path('image/service-candidate');
					$filename = $file_path.'/'.$candidate_musaned;
					$data = [
						'name' => $cand_name,
						'party_name' => $party_name,
						'agency' => $party_agency,
						'passport' => $pass_no,
						'musaned_date' => date("d-m-Y",strtotime($musaned_date)) ,
					];

					// Message Data
					$whsbody = "Dear ".$party_name."\n".ucfirst($party_agency)."\nThe Candidate ".$cand_name." bearing passport No.".$pass_no." successfully registered at Musaned on ".date("d-m-Y",strtotime($musaned_date)).".\nPlease check the below attachment of Musaned CV for your references.";
					$ins = "f102f804a510c482fff09a29bf31e309902dbe276cb87844047d53797382d4be";
				    $api = "717bc8c88c24fa3b19a2c923e402d1c9c09fa674282f22e30c7b67729641f00a";

					$url_path = url('/');
					$file_path2 = 'image/service-candidate';
					// $media = $url_path.'/'.$file_path2.'/'.$candidate_musaned;

					$media = url('/image/service-candidate/'.$candidate_musaned);
					
					
					$ext = pathinfo($candidate_musaned,PATHINFO_EXTENSION);
					if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg'){
						$type = "image";
					}
					if($ext == 'pdf'){
						$type = "document";
					}

					if($ext == 'mp4'){
						$type = "video";
					}


					// $type = "image";
					// $type = $ext;
					$url4 = "http://whatsapi.smsinsta.com/api/send-media";
					$url5 = "http://whatsapi.smsinsta.com/api/send-text";
					
					// Send Mail and Whatsapp to the Party
					if ($party_email != '' && $party_email2 == '') {
						Mail::to($party_email)->send(new MusanedRegister($filename,$mesSub,$data));
					}

					if ($party_email != '' && $party_email2 != '') {
						Mail::to($party_email)->cc($party_email2)->send(new MusanedRegister2($filename,$mesSub,$data));
					}					

				}

				if(isset($user)){
					if($user->user_id == '200'){
						$care_of_email = '';
						$care_of_mobile = '';
					}else{
						$care_of_email = $user->email;
						$care_of_mobile = '91'.$user->mobile;
					}


					if (isset($party)) {
						$party_name = $party->pty_full_name;
						$party_agency = $party->pty_ag_name;
					}else{
						$party_name = '';
						$party_agency = '';
					}

					// Mail Data
					$mesSub = 'Musaned Registered Successfully for '.$cand_name.' with '.$pass_no;
					$file_path = public_path('image/service-candidate');
					$filename = $file_path.'/'.$candidate_musaned;
					$data = [
						'name' => $cand_name,
						'party_name' => $user->name,
						'agency' => $party_agency,
						'passport' => $pass_no,
						'musaned_date' => date("d-m-Y",strtotime($musaned_date)) ,
					];

					// Message Data
					$whsbody = "Dear ".$user->name."\n".$party_agency."\nThe Candidate ".$cand_name." bearing passport No.".$pass_no." successfully registered at Musaned on ".date("d-m-Y",strtotime($musaned_date)).".\nPlease check the below attachment of Musaned CV for your references.";
					$ins = "f102f804a510c482fff09a29bf31e309902dbe276cb87844047d53797382d4be";
				    $api = "717bc8c88c24fa3b19a2c923e402d1c9c09fa674282f22e30c7b67729641f00a";

					$url_path = url('/');
					$file_path2 = 'image/service-candidate';
					// $media = $url_path.'/'.$file_path2.'/'.$candidate_musaned;

					$media = url('/image/service-candidate/'.$candidate_musaned);

					$ext = pathinfo($candidate_musaned,PATHINFO_EXTENSION);
					if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg'){
						$type = "image";
					}
					if($ext == 'pdf'){
						$type = "document";
					}

					if($ext == 'mp4'){
						$type = "video";
					}

					// $type = "image";
					$url4 = "http://whatsapi.smsinsta.com/api/send-media";
					$url5 = "http://whatsapi.smsinsta.com/api/send-text";
					

					if ($care_of_email != '') {
						Mail::to($care_of_email)->send(new MusanedRegister3($filename,$mesSub,$data));
					}


					// Testing For Queu
					// if ($user->mobile != '') {
					// 	// Media Send
					// 	$data6 = [
					// 		"number" => $care_of_mobile,
					// 		"msg" => '',
					// 		"media" => $media,
					// 		"type" => $type,
					// 		"instance" => $ins,
					// 		"apikey" => $api
					// 	];

					// 	$ch = curl_init();
					// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
					// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
					// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data6));
					// 	curl_setopt($ch, CURLOPT_URL, $url4);
					// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					// 	$result = curl_exec($ch);
					// 	curl_close($ch);

					// 	// Text Send
					// 	$data7 = [
					// 		"number" => $care_of_mobile,
					// 		"msg" => $whsbody,
					// 		// "media" => $media,
					// 		// "type" => $type,
					// 		"instance" => $ins,
					// 		"apikey" => $api
					// 	];

					// 	$ch = curl_init();
					// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
					// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
					// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data7));
					// 	curl_setopt($ch, CURLOPT_URL, $url5);
					// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					// 	$result = curl_exec($ch);
					// 	curl_close($ch);
					// }
				}

				/* Temporary Hide End */


				// Dummy Party and User
				$party = Party::where('pty_id','=',$pty_id)->first();
				$user = User::where('user_id','=',$care_of_id)->first();

				// Store in Candidate Job List
				$post = new CandidateListJpb();
				$post->cand_id = $cand_id;
				$post->pty_id = $pty_id;
				$post->care_of_id = $care_of_id;
				$post->user_id = Auth::user()->user_id;
				$post->file = $candidate_musaned;
				$ext2 = pathinfo($candidate_musaned,PATHINFO_EXTENSION);
				if($ext2 == 'jpg' || $ext2 == 'png' || $ext2 == 'jpeg'){
					$type = "image";
				}
				if($ext2 == 'pdf'){
					$type = "document";
				}

				if($ext2 == 'mp4'){
					$type = "video";
				}
				$post->file_ext = $type;
				$post->send_type = 'registered';
				$post->musaned_date	=$musaned_date;
				$post->save();

				MusanedJob::dispatch($post,$party,$user,$cand_sd,$getAPI)->onQueue('visaservice');


			}

			if($musaned_registered == 'add_another_office'){
				
				// Update Another Office Details
				if($request->has('office_imgs')){
					$file = $request->file('office_imgs');
					$file_count = File::files(base_path().'/public/image/service-candidate');
					$filecount = 0;
					if ($file_count !== false) {
						$filecount = count($file_count);
					}
					$file_exe = $file->getClientOriginalExtension();
					$name = $filecount . '.' . $file_exe;
					$file->move(base_path().'/public/image/service-candidate', $name);
					$candidate_musaned_s = $name;
	
					$ser_d->office_imgs = $candidate_musaned_s;
				}

				$ser_d->office_name = $request->office_name;
				$ser_d->office_email = $request->office_email;
				$ser_d->office_mobile = $request->office_mobile;
				
				if($add_another_office == 0 && $add_another_office !=''){
					// update in Service Details
					$ser_d->add_another_office = $add_another_office;
					// $ser_d->musaned_user_by = Auth::user()->user_id;
					$ser_d->save();
					// Update Service Status
					$ser_s->musaned = 1;
					$ser_s->save();
					// Update in Candidate Status
					$cand_status->active_status = 'Waiting For Mofa Generate';
					$cand_status->active_sub_status = '';
					$cand_status->incomplete_status = 'Waiting for Deleting from another office';
					

					// Update in Activities
					date_default_timezone_set('Asia/Kolkata');
					$timeline = new Timeline();
					$timeline->cand_id = $cand_id;
					$timeline->subject = 'Forward Mofa without musaned';
					$timeline->time = date("h:i:sa");
					$timeline->user_id = Auth::user()->user_id;
					$timeline->status = 1;
					$timeline->save();
					
				}

				if($add_another_office == 1 && $add_another_office !=''){
					// Update in Service Details
					$ser_d->add_another_office = $add_another_office;
					// $ser_d->musaned_user_by = Auth::user()->user_id;
					$ser_d->save();
					// Update in Service Status
					$ser_s->musaned = 0;
					$ser_s->save();

					$cand_status->active_status = 'Waiting for Deleting from another office';
					$cand_status->active_sub_status = '';
					$cand_status->incomplete_status = 'Waiting for Deleting from another office';

					// Update in Activities
					date_default_timezone_set('Asia/Kolkata');
					$timeline = new Timeline();
					$timeline->cand_id = $cand_id;
					$timeline->subject = 'Waiting for deleting from another office';
					$timeline->time = date("h:i:sa");
					$timeline->user_id = Auth::user()->user_id;
					$timeline->status = 1;
					$timeline->save();
				}

				$ser_d->musaned_registered = $musaned_registered;
				
				// Update Candidat Status
				
				$cand_status->musaned_rejection = 1;
				$cand_status->musaned_registered = 0;
				$cand_status->save();
	
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Musaned Registered at another office';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 1;
				$timeline->save();
	
				// Get Details for Send Mail and Whatsapp
				// if (isset($party)) {
				// 	$party_name = $party->pty_full_name;
				// 	$party_agency = $party->pty_ag_name;

				// 	if($party == '200'){
				// 		// Primary Contact
				// 		$party_email = '';
				// 		$party_contact = '';
				// 		// Secondary Details
				// 		$party_email2 = '';
				// 		$party_contact2 = '';
				// 	}else{
				// 		// Primary Contact
				// 		$party_email = $party->pty_email;
				// 		$party_contact = '91'.$party->pty_comp_contact;
				// 		// Secondary Details
				// 		$party_email2 = $party->sec_email;
				// 		$party_contact2 = '91'.$party->pty_contact_no;
				// 	}



				// 	// Mail Data
				// 	$mesSub = 'Musaned regitered at another office of '.$cand_name.' with '.$pass_no;
				// 	$file_path = public_path('image/service-candidate');
				// 	$filename = $file_path.'/'.$candidate_musaned_s;
				// 	$data = [
				// 		'name' => $cand_name,
				// 		'party_name' => $party_name,
				// 		'agency' => $party_agency,
				// 		'passport' => $pass_no,
				// 		'musaned_date' => date("d-m-Y",strtotime($musaned_date)) ,
				// 	];

				// 	// Message Data
				// 	$whsbody = "Dear ".$party_name."\n".ucfirst($party_agency)."\nThe Candidate ".$cand_name." bearing passport No. ".$pass_no." of Musaned has been Registered at another office.";
				// 	$ins = "f102f804a510c482fff09a29bf31e309902dbe276cb87844047d53797382d4be";
	    		// 	$api = "717bc8c88c24fa3b19a2c923e402d1c9c09fa674282f22e30c7b67729641f00a";
				// 	$url_path = url('/');
				// 	$file_path2 = 'image/service-candidate';
				// 	$media = $url_path.'/'.$file_path2.'/'.$candidate_musaned_s;

				// 	$ext = pathinfo($candidate_musaned_s,PATHINFO_EXTENSION);
				// 	if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg'){
				// 		$type = "image";
				// 	}
				// 	if($ext == 'pdf'){
				// 		$type = "document";
				// 	}

				// 	if($ext == 'mp4'){
				// 		$type = "video";
				// 	}

				// 	// $type = "image";
				// 	$url4 = "http://whatsapi.smsinsta.com/api/send-media";
				// 	$url5 = "http://whatsapi.smsinsta.com/api/send-text";
					
				// 	// Send Mail and Whatsapp to the Party
				// 	if ($party_email != '' && $party_email2 == '') {
				// 		Mail::to($party_email)->send(new MusanedRegister($filename,$mesSub,$data));
				// 	}

				// 	if ($party_email != '' && $party_email2 != '') {
				// 		Mail::to($party_email)->cc($party_email2)->send(new MusanedRegister2($filename,$mesSub,$data));
				// 	}

				// 	// Testing for queue

				// 	// if ($party->pty_comp_contact != '') {
				// 	// 	// Send Media
				// 	// 	$data4 = [
				// 	// 		"number" => $party_contact,
				// 	// 		"msg" => '',
				// 	// 		"media" => $media,
				// 	// 		"type" => $type,
				// 	// 		"instance" => $ins,
				// 	// 		"apikey" => $api
				// 	// 	];

				// 	// 	$ch = curl_init();
				// 	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
				// 	// 	curl_setopt($ch, CURLOPT_URL, $url4);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	// 	$result = curl_exec($ch);
				// 	// 	curl_close($ch);
				// 	// 	// Send Text
				// 	// 	$data41 = [
				// 	// 		"number" => $party_contact,
				// 	// 		"msg" => $whsbody,
				// 	// 		// "media" => $media,
				// 	// 		// "type" => $type,
				// 	// 		"instance" => $ins,
				// 	// 		"apikey" => $api
				// 	// 	];

				// 	// 	$ch = curl_init();
				// 	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data41));
				// 	// 	curl_setopt($ch, CURLOPT_URL, $url5);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	// 	$result = curl_exec($ch);
				// 	// 	curl_close($ch);
				// 	// }

				// 	// if ($party->pty_contact_no != '') {
				// 	// 	$data5 = [
				// 	// 		"number" => $party_contact2,
				// 	// 		"msg" => $whsbody,
				// 	// 		"media" => $media,
				// 	// 		"type" => $type,
				// 	// 		"instance" => $ins,
				// 	// 		"apikey" => $api
				// 	// 	];

				// 	// 	$ch = curl_init();
				// 	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
				// 	// 	curl_setopt($ch, CURLOPT_URL, $url4);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	// 	$result = curl_exec($ch);
				// 	// 	curl_close($ch);
				// 	// }

					

				// }

				// Send Whatsapp and Mail
				// $user = User::where('user_id','=',$care_of_id)->first();
				// $party = Party::where('pty_id','=',$pty_id)->first();
				// if(isset($user)){

				// 	if($user->user_id == '200'){
				// 		$care_of_email = '';
				// 		$care_of_mobile = '';
				// 	}else{
				// 		$care_of_email = $user->email;
				// 		$care_of_mobile = '91'.$user->mobile;
				// 	}


				// 	if (isset($party)) {
				// 		$party_name = $party->pty_full_name;
				// 		$party_agency = $party->pty_ag_name;
				// 	}else{
				// 		$party_name = '';
				// 		$party_agency = 'None';
				// 	}

				// 	// Mail Data
				// 	$mesSub = 'Musaned regitered at another office of '.$cand_name.' with '.$pass_no;
				// 	$file_path = public_path('image/service-candidate');
				// 	$filename = $file_path.'/'.$candidate_musaned_s;
				// 	$data = [
				// 		'name' => $cand_name,
				// 		'party_name' => $user->name,
				// 		'agency' => $party_agency,
				// 		'passport' => $pass_no,
				// 		'musaned_date' => date("d-m-Y",strtotime($musaned_date)) ,
				// 	];

				// 	// Message Data
				// 	$whsbody = "Dear ".$user->name."\n".$party_agency."\nThe Candidate ".$cand_name." bearing passport No. ".$pass_no." of Musaned has been Registered at another office.";
				// 	$ins = "f102f804a510c482fff09a29bf31e309902dbe276cb87844047d53797382d4be";
	    		// 	$api = "717bc8c88c24fa3b19a2c923e402d1c9c09fa674282f22e30c7b67729641f00a";
				// 	$url_path = url('/');
				// 	$file_path2 = 'image/service-candidate';
				// 	$media = $url_path.'/'.$file_path2.'/'.$candidate_musaned_s;

				// 	$ext = pathinfo($candidate_musaned_s,PATHINFO_EXTENSION);
				// 	if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg'){
				// 		$type = "image";
				// 	}
				// 	if($ext == 'pdf'){
				// 		$type = "document";
				// 	}

				// 	if($ext == 'mp4'){
				// 		$type = "video";
				// 	}

				// 	// $type = "image";
				// 	$url4 = "http://whatsapi.smsinsta.com/api/send-media";
				// 	$url5 = "http://whatsapi.smsinsta.com/api/send-text";


				// 	if ($care_of_email != '') {
				// 		Mail::to($care_of_email)->send(new MusanedRegister3($filename,$mesSub,$data));
				// 	}


				// 	// For Testing Queue
				// 	// if ($user->mobile != '') {
				// 	// 	// Send Media
				// 	// 	$data6 = [
				// 	// 		"number" => $care_of_mobile,
				// 	// 		"msg" => '',
				// 	// 		"media" => $media,
				// 	// 		"type" => $type,
				// 	// 		"instance" => $ins,
				// 	// 		"apikey" => $api
				// 	// 	];

				// 	// 	$ch = curl_init();
				// 	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data6));
				// 	// 	curl_setopt($ch, CURLOPT_URL, $url4);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	// 	$result = curl_exec($ch);
				// 	// 	curl_close($ch);
				// 	// 	// Send Text
				// 	// 	$data61 = [
				// 	// 		"number" => $care_of_mobile,
				// 	// 		"msg" => $whsbody,
				// 	// 		// "media" => $media,
				// 	// 		// "type" => $type,
				// 	// 		"instance" => $ins,
				// 	// 		"apikey" => $api
				// 	// 	];

				// 	// 	$ch = curl_init();
				// 	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data61));
				// 	// 	curl_setopt($ch, CURLOPT_URL, $url5);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	// 	$result = curl_exec($ch);
				// 	// 	curl_close($ch);
				// 	// }
				// }


				// Store in Candidate Job List
				$post = new CandidateListJpb();
				$post->cand_id = $cand_id;
				$post->pty_id = $pty_id;
				$post->care_of_id = $care_of_id;
				$post->user_id = Auth::user()->user_id;
				$post->file = $candidate_musaned_s;
				$ext2 = pathinfo($candidate_musaned_s,PATHINFO_EXTENSION);
				if($ext2 == 'jpg' || $ext2 == 'png' || $ext2 == 'jpeg'){
					$type = "image";
				}
				if($ext2 == 'pdf'){
					$type = "document";
				}

				if($ext2 == 'mp4'){
					$type = "video";
				}
				$post->file_ext = $type;
				$post->send_type = 'add_another_office';
				$post->save();

				MusanedJob::dispatch($post,$party,$user,$cand_sd,$getAPI)->onQueue('visaservice');

			}

			
			// Update Candidate Status
			$cand_status->waiting_for_musaned_registration = 1;
			$cand_status->save();

			// Update in Service Detaisl
			$ser_d->musaned_user_by = Auth::user()->user_id;
			$ser_d->save();
			

			Session::flash('success', 'Musaned Details Updated!');
			return redirect('services/candidate-status/' . $cand_id);

		} else {
			Session::flash('error', 'Please Select Musaned is Registered or Not');
			return redirect('services/candidate-status/' . $cand_id);
		}
		

		
	}

	public function update_musaned_fr(Request $request){
		$cand_id = $request->input('cand_id');
		$pass_no = $request->input('passport');
		$musaned_registered = $request->input('musaned_registered');
		$musaned_date = $request->input('musaned_date');
		$add_another_office = $request->input('add_another_office');

		// Candidate Status
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		// Service Details
		$ser_d = ServiceDetails::where('cand_id','=',$cand_id)->first();
		// Service Status
		$ser_s = ServiceStatus::where('cand_id','=',$cand_id)->first();

		// Update in Candidate Status New
		// $cand_st_new = CandStatusNew::where('cand_id','=',$cand_id)->first();
		$cand_nst = ComplCandStatus::where('cand_id','=',$cand_id)->first();

		// Main Status
		$finalStatus = new CandCompFinalStatus();

		// Update in Stage and Status
		// $stage_f = CandStageF::where('cand_id','=',$cand_id)->first();
		// $status_f = CandCstatusF::where('cand_id','=',$cand_id)->first();
		// $incom_f = CandIncomF::where('cand_id','=',$cand_id)->first();


		if($musaned_registered != ''){

			if($musaned_registered == 'registered'){
				$ser_d->musaned_registered = $musaned_registered;
				$ser_d->musaned_date = $musaned_date;
				
				$ser_d->musaned_user_by = Auth::user()->user_id;

				if($request->has('musaned_doc')){
					$file = $request->file('musaned_doc');
					$file_count = File::files(base_path().'/public/image/service-candidate');
					$filecount = 0;
					if ($file_count !== false) {
						$filecount = count($file_count);
					}
					$file_exe = $file->getClientOriginalExtension();
					$name = $filecount . '.' . $file_exe;
					$file->move(base_path().'/public/image/service-candidate', $name);
					$candidate_musaned = $name;
	
					$ser_d->musaned_doc = $candidate_musaned;
				}

				// Update in Service Status
				$ser_s->musaned_del = 1;
				$ser_s->save();
			
				// Candidate Status Upate
				$cand_status->musaned_registered = 1;
				$cand_status->musaned_rejection = 0;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = 'Mofa Paid';
				$cand_status->incomplete_status = '';
				$cand_status->save();

				// Update New Candidate Status
				$cs_status = array($cand_nst->c_status,'Musaned Registered');
				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->save();

				$chk_mus = CandCompFinalStatus::where('final_status','=','Musaned Registered')->where('cand_id','=',$cand_id)->first();
				if($chk_mus == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Musaned Registered";
					$finalStatus->save();
				}


				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 0;
				// $stage_f->waiting_for_med_fit = 1;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// $status_f->musaned_reject = 1;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->mofa_unpaid = 0;
				// $incom_f->waiting_for_del_another_off = 0;
				// $incom_f->mofa_issued_wo_musaned = 0;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();
				


				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = '';
				// $cand_st_new->stc_status = 'Mofa Paid';
				// $cand_st_new->save();

				// Update in Activities
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Musaned Registered';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 1;
				$timeline->save();
			}

			if($musaned_registered == 'add_another_office'){

				// Update in Service Details
				$ser_d->add_another_office = $add_another_office;
				// $ser_d->musaned_user_by = Auth::user()->user_id;
				$ser_d->save();
				// Update in Service Status
				$ser_s->musaned_del = 0;
				$ser_s->save();


				// Update in Candidate Status
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = 'Mofa Paid';
				$cand_status->incomplete_status = 'Mofa Issued without musaned';

				$chk_mus = CandCompFinalStatus::where('final_status','=','Musaned Registered')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_mus)){
					$chk_mus->delete();
				}

				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 0;
				// $stage_f->waiting_for_med_fit = 1;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();


				// $incom_f->mofa_unpaid = 0;
				// $incom_f->waiting_for_del_another_off = 1;
				// $incom_f->mofa_issued_wo_musaned = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

				// Update in new candidate

				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = 'Waiting for Deleting from another office';
				// $cand_st_new->stc_status = 'Mofa Paid';
				// $cand_st_new->save();


				// Update in Activities
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Waiting for deleting from another office';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 1;
				$timeline->save();




				

				

				$ser_d->musaned_registered = $musaned_registered;
				
				// Update Candidat Status
				
				$cand_status->musaned_rejection = 1;
				$cand_status->musaned_registered = 0;
				$cand_status->save();
		
			}



			// Update Candidate Status
			$cand_status->waiting_for_musaned_registration = 1;
			$cand_status->save();

			// Update in Service Detaisl
			$ser_d->save();
			

			Session::flash('success', 'Musaned Details Updated!');
			return redirect('services/candidate-status/' . $cand_id);

		}else{
			Session::flash('error', 'Please Select Musaned is Registered or Not');
			return redirect('services/candidate-status/' . $cand_id);
		}
	}

	public function mofa_store(Request $request) {

		$mofa_no = $request->input('mofa_no');
		$mofa_status = $request->input('mofa_status');
		$passport_status = $request->input('passport_status');
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$mofa_rel = $request->input('mofa_rel');

		// Get Musaned
		$ser_m_musaned = ServiceDetails::where('cand_id','=',$cand_id)->first();
		$mofa_iss_wm = $ser_m_musaned->add_another_office; 

		// Candidate Status
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		
		// Update in Candidate Status New
		// $cand_st_new = CandStatusNew::where('cand_id','=',$cand_id)->first();


		// Update in Stage and Status
		// $stage_f = CandStageF::where('cand_id','=',$cand_id)->first();
		// $status_f = CandCstatusF::where('cand_id','=',$cand_id)->first();
		// $incom_f = CandIncomF::where('cand_id','=',$cand_id)->first();


		// if ($request->hasFile('mofa_screenshots')) {
		// 	$file = $request->file('mofa_screenshots');
		// 	$file_count = File::files(base_path().'/public/status-images/mofa_screenshots');
		// 	$filecount = 0;

		// 	if ($file_count !== false) {
		// 		$filecount = count($file_count);
		// 	}
		// 	$file_exe = $file->getClientOriginalExtension();
		// 	$name = $filecount . '.' . $file_exe;

		// 	$file->move(base_path().'/public/status-images/mofa_screenshots', $name);
			
		// 	$mofa_screenshots = $name;

		// } else {
		// 	$mofa_screenshots = '';
		// }

		// find religion from candidate
		$cand_data_r = Candidate::find($cand_id);
		$cand_rel = $cand_data_r->rel_cst_id;

		// check exist or create in database
		// $incom_chk = IncompleteStage::where('cand_id','=',$cand_id)->where('incomplete','=','Mofa Unpaid')->first();
		// $incom_chk_pre1 = IncompleteStage::where('cand_id','=',$cand_id)->where('incomplete','=','Mofa Unpaid')->where('status','=','0')->first();
		// $incom_st = new IncompleteStage();



		if ($cand_rel == $mofa_rel) {

			if($mofa_rel != ''){
				$mofar = new MofaReligion();
				$mofar->cand_id = $cand_id;
				$mofar->cand_passport_no = $pass_no;
				$mofar->mofa_religion = $mofa_rel;
	
				$mofar->save();
	
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Mofa Religion';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 10;
				$timeline->save();
	
			}

			$pass_data = array(
				'mofa_screenshots' => '',
				'mofa_no' => $mofa_no,
				'mofa_status' => $mofa_status,
				'passport_status' => $passport_status,
				'created_at' =>  Carbon::now()->toDateTimeString()
			);



			if ($mofa_status == 0) {
				// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
				$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
				$ser_status->mofa_unpaid = 1;
				$ser_status->mofa_paid = 0;
				$ser_status->save();

				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Mofa Unpaid';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 4;
				$timeline->save();
			}
			if ($mofa_status == 1) {
				// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
				$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
				$ser_status->mofa_paid = 1;
				$ser_status->mofa_unpaid = 0;
				$ser_status->save();

				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Mofa Paid';
				$timeline->time = date("h:i:sa");
				$timeline->status = 1;
				$timeline->user_id = Auth::user()->user_id;
				$timeline->save();
			}
		
			// if(!isset($incom_chk)){
			// 	// check previouse
			// 	// $incom_chk_pre1->status = 1;
			// 	// $incom_chk_pre1->user_id = Auth::user()->user_id;
			// 	// $incom_chk_pre1->save();
			// 	// create new incomplete status
			// 	$incom_st->cand_id = $cand_id;
			// 	$incom_st->incomplete = "Mofa Unpaid";
			// 	$incom_st->user_id = Auth::user()->user_id;
			// 	$incom_st->save();
			// }

			if ($passport_status == 0) {
				// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
				$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
				$ser_status->passport_copy = 1;
				$ser_status->passport_original = 0;
				$ser_status->save();

				// Update in Candidate Status
				
				$cand_status->waiting_for_og_passport = 1;
				
				$cand_status->waiting_mofa = 1;
				$cand_status->active_status = 'Mofa Unpaid';
				$cand_status->active_sub_status = '';
								
				
				$cand_status->incomplete_status = 'Mofa Unpaid';


				


				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 1;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// $incom_f->mofa_unpaid = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();


				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Passport Copy Recieved';
				$timeline->time = date("h:i:sa");
				$timeline->status = 4;
				$timeline->user_id = Auth::user()->user_id;
				$timeline->save();
			}
			if ($passport_status == 1) {
				// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
				$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
				$ser_status->passport_original = 1;
				$ser_status->passport_copy = 0;
				
				$ser_status->awaited_passport = 1;
				$ser_status->save();

				$cand_status->waiting_mofa = 1;
				$cand_status->active_status = 'Mofa Unpaid';
				$cand_status->active_sub_status = '';
				$cand_status->incomplete_status = 'Mofa Unpaid';

				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 1;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// $incom_f->mofa_unpaid = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Passport Original Recieved';
				$timeline->time = date("h:i:sa");
				$timeline->status = 1;
				$timeline->user_id = Auth::user()->user_id;
				$timeline->save();
			}

			

			$pass_data = serialize($pass_data);
			// $sd = ServiceDetails::where('cand_passport_no', '=', $pass_no)->first();
			$sd = ServiceDetails::where('cand_id', '=', $cand_id)->first();
			$sd->mofa = $pass_data;
			$sd->save();

			// Insert Mofa Details in qr_mofa_details
			$mofa_d_u = new MofaDetail();
			$mofa_d_u->cand_id = $cand_id;
			$mofa_d_u->mofa_status = 1;
			$mofa_d_u->mofa_no = $mofa_no;
			// $mofa_d_u->mofa_screenshot = $mofa_screenshots;
			$mofa_d_u->user_id = Auth::user()->user_id;
			$mofa_d_u->save();

			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->mofa = 1;
			$ser_status->save();





			$cand_status->save();

			// Cand New Status
			// $cand_st_new->stage = 'Mofa Unpaid';
			// $cand_st_new->st_pending = 'Mofa Unpaid';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject = 'Mofa Created';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();


			// check Passport Type is ECR
			// $pass_type = Candidate::find($cand_id);
			// if($pass_type->cand_passport_type == 'ECR'){
			// 	$post_emig = new Emigration();
			// 	$post_emig->cand_id = $cand_id;
			// 	$post_emig->pass_no = $pass_type->cand_passport_no;
			// 	$post_emig->cand_name = $pass_type->cand_fname.' '.$pass_type->cand_lname;
			// 	$post_emig->pty_id = $pass_type->pty_id;
			// 	$post_emig->save();

			// 	// Insert Status
			// 	$last_emig_id = $post_emig->id;
			// 	$post_emig_st = new EmigrationStatus();
			// 	$post_emig_st->emig_id = $last_emig_id;
			// 	$post_emig_st->cand_id = $cand_id;
			// 	$post_emig_st->pty_id = $pass_type->pty_id;
			// 	$post_emig_st->status_name = 'New Documents';
			// 	$post_emig_st->save();
			// }



			Session::flash('success', 'Mofa created successfully !');
			return redirect('services/candidate-status/' . $cand_id);
		} else {
			Session::flash('error', 'Insert Proper Data !');
			return redirect('services/candidate-status/' . $cand_id);
		}
		

	}

	public function passport_copy_store(Request $request){
        // dd($request);
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$passport_status = $request->input('passport_status');



		if ($passport_status == 0) {
            // dd(1);
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->passport_copy = 1;
			$ser_status->passport_original = 0;
			$ser_status->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Passport Copy Recieved';
			$timeline->time = date("h:i:sa");
			$timeline->status = 4;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
		}
		if ($passport_status == 1) {
            // dd(2);
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->passport_original = 1;
			$ser_status->passport_copy = 0;
			$c= $ser_status->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Passport Original Recieved';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
            // dd($c);
		}
		Session::flash('success', 'Passport status change successfully !');
		return redirect('services/candidate-status/' . $cand_id);
	}

	public function mofa_unpaid_store(Request $request){

		
		global $mofa_status;
		$pass_no = $request->input('passport');
		$ppplication_fee = $request->input('Application_Fee') ? $request->input('Application_Fee') : '';
		$medical_fee = $request->input('Medical_Fee') ? $request->input('Medical_Fee') : '';
		$cand_id = $request->input('cand_id');

		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		
		$cand_nst = ComplCandStatus::where('cand_id','=',$cand_id)->first();
		$cs_status = array($cand_nst->c_status,"Mofa Paid");
		// dd($cs_status);

		$st_data = ServiceStatus::where('cand_id','=',$cand_id)->first();
		$ser_data = ServiceDetails::where('cand_id','=',$cand_id)->first();	
		
		$pass_data = unserialize($ser_data->mofa);
		
		$pass_data['Application_Fee'] = $ppplication_fee;
		$pass_data['Medical_Fee'] = $medical_fee;

		// Update in Candidate Status New
		// $cand_st_new = CandStatusNew::where('cand_id','=',$cand_id)->first();

		// Update Details of Fees and Date

		// Main Status
		$finalStatus = new CandCompFinalStatus();

		$pass_data_ser = serialize($pass_data);
		$ser_data->mofa = $pass_data_ser;


		if($ppplication_fee == 1 && $ser_data->application_fee_date ==''){
			$ser_data->application_fee_date = Carbon::now();
		}
		if($medical_fee == 1 && $ser_data->medical_fee_date ==''){
			$ser_data->medical_fee_date = Carbon::now();
		}
		
		$ser_data->update();

		// dd($pass_data);


		if($pass_data['Application_Fee'] != 1 || $pass_data['Medical_Fee'] !=1 ||  $pass_data['Application_Fee'] == '' || $pass_data['Medical_Fee'] == ''){
			$st_data->mofa_unpaid = 1;
			$st_data->passport_original = 0;
			$st_data->mofa_paid = 0;
			$st_data->update(); 

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Mofa Unpaid';
			$timeline->time = date("h:i:sa");
			$timeline->status = 4;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();

			Session::flash('success', 'Mofa is Unpaid, Please Paid Mofa !');
			return redirect('services/candidate-status/' . $cand_id);
		}else{
			$st_data->mofa_paid = 1;
			$st_data->mofa_unpaid = 0;
			$st_data->passport_original = 1;
			$st_data->update();

			// $st_data->update();
			// $ser_data->mofa_payment_data = Carbon::now();

			// Update Mofa Paid Status
			$cand_status->mofapaid = 1;

			// Update Candidate Status
			// $cand_st_new->stage = 'Waiting For Medical Fitness';
			if($st_data->musaned_del == 1){

				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = 'Mofa Paid';
				$cand_status->incomplete_status = '';


				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->Mofa_Paid = 1;
				$cand_nst->save();

				// check mofa paid is exist
				$chk_mofa = CandCompFinalStatus::where('final_status','=','Mofa Paid')->where('cand_id','=',$cand_id)->first();
				if($chk_mofa == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Mofa Paid";
					$finalStatus->save();
				}

			}
			if($st_data->musaned_del == 0){

				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = 'Mofa Paid';
				$cand_status->incomplete_status = 'Mofa Issued without musaned';


				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->Mofa_Paid = 1;
				$cand_nst->save();

				// check mofa paid is exist
				$chk_mofa = CandCompFinalStatus::where('final_status','=','Mofa Paid')->where('cand_id','=',$cand_id)->first();
				if($chk_mofa == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Mofa Paid";
					$finalStatus->save();
				}

			}			

			$cand_status->save();


			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Mofa Paid';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();

			// Find Candidate Details
			$cand_d = Candidate::where('cand_id','=',$cand_id)->first();
			$party = Party::where('pty_id','=',$cand_d->pty_id)->first();
			$care_of_id = $cand_d->care_of_id;
			$user = User::where('user_id','=',$care_of_id)->first();
			
			//Update in Mofa Back Details
			$backmofaupdt = new Mofabackupdetails();
			$backmofaupdt->mofa_no = $pass_data['mofa_no'];
			$backmofaupdt->mofa_data = $pass_data_ser;
			$backmofaupdt->mofa_issue_date = date('Y-m-d h:i');
			$backmofaupdt->user_id = Auth::user()->user_id;
			$backmofaupdt->cand_id = $cand_id;
			$backmofaupdt->application_fee = $ppplication_fee;
			$backmofaupdt->medical_fee = $medical_fee;
			$backmofaupdt->application_paid_date = date('Y-m-d h:i');
			$backmofaupdt->medical_paid_date = date('Y-m-d h:i');
			$backmofaupdt->mofa_religion = $cand_d->rel_cst_id;
			$backmofaupdt->mofa_paid = 1;
			$backmofaupdt->save();			
			
			
			// Get Service API
			$getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();

			if(isset($user)){

				if($user->user_id == '200'){
					$care_of_email = '';
					$care_of_mobile = '';
				}else{
					$care_of_email = $user->email;
					$care_of_mobile = '91'.$user->mobile;
				}



				if(isset($party)){
					$party_name = $party->pty_full_name;
					$party_agency = $party->pty_ag_name;
				}else{
					$party_name = '';
					$party_agency = '';
				}

				$cand_name = $cand_d->cand_fname.' '.$cand_d->mname.' '.$cand_d->cand_lname;
				$cand_pass_no = $cand_d->cand_passport_no;
				$mofa_no = $pass_data['mofa_no'];
				// Send Mail Notification
				$mesSubjc = 'MOFA Number Generated Successfully for '.$cand_name.' with '.$cand_pass_no;
				$data = [
					'name' => $cand_name,
					'party_name' => $user->name,
					'agency' => $party_agency,
					'passport' => $cand_pass_no,
					'mofa_no' => $mofa_no
				];


				// Send Whatsapp Notification
				$whsbody = "Dear ".$user->name."\n".$party_agency."\nThe MOFA No. ".$mofa_no." has been successfully generated of the Candidate. ".$cand_name." bearing Passport No. ".$cand_pass_no;
				$ins = "f102f804a510c482fff09a29bf31e309902dbe276cb87844047d53797382d4be";
				$api = "717bc8c88c24fa3b19a2c923e402d1c9c09fa674282f22e30c7b67729641f00a";
				$url2 = "http://whatsapi.smsinsta.com/api/send-text";

				if($care_of_email != ''){
					Mail::to($care_of_email)->send(new MofaNotification3($data,$mesSubjc));
				}

				// Testing Purpose
				// if($user->mobile != ''){
				// 	$data4 = [
				// 		"number" => $care_of_mobile,
				// 		"msg" => $whsbody,
				// 		"instance" => $ins,
				// 		"apikey" => $api
				// 	];
				// 	$ch = curl_init();
				// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
				// 	curl_setopt($ch, CURLOPT_URL, $url2);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	$result = curl_exec($ch);
				// 	curl_close($ch);
				// }


			}


			if(isset($party)){
				$cand_name = $cand_d->cand_fname.' '.$cand_d->mname.' '.$cand_d->cand_lname;
				$cand_pass_no = $cand_d->cand_passport_no;
				$mofa_no = $pass_data['mofa_no'];
				$party_name = $party->pty_full_name;
				$party_agency = $party->pty_ag_name;

				if($party->pty_id == '200'){
					// Primary Contact
					$party_email = '';
					$party_contact = '';
					// Secondary Contact
					$party_email2 = '';
					$party_contact2 = '';
				}else{
					// Primary Contact
					$party_email = $party->pty_email;
					$party_contact = '91'.$party->pty_comp_contact;
					// Secondary Contact
					$party_email2 = $party->sec_email;
					$party_contact2 = '91'.$party->pty_contact_no;
				}



				// Send Mail Notification
				$mesSubjc = 'MOFA Number Generated Successfully for '.$cand_name.' with '.$cand_pass_no;
				$data = [
					'name' => $cand_name,
					'party_name' => $party_name,
					'agency' => $party_agency,
					'passport' => $cand_pass_no,
					'mofa_no' => $mofa_no
				];

				if($party_email !='' && $party_email2 != ''){
					Mail::to($party_email)->cc($party_email2)->send(new MofaNotification($data,$mesSubjc));
				}

				if($party_email !='' && $party_email2 == ''){
					Mail::to($party_email)->send(new MofaNotification2($data,$mesSubjc));
				}

				// Send Whatsapp Notification
				$whsbody = "Dear ".$party_name."\n".ucfirst($party_agency)."\nThe MOFA No. ".$mofa_no." has been successfully generated of the Candidate. ".$cand_name." bearing Passport No. ".$cand_pass_no;
				$ins = "f102f804a510c482fff09a29bf31e309902dbe276cb87844047d53797382d4be";
				$api = "717bc8c88c24fa3b19a2c923e402d1c9c09fa674282f22e30c7b67729641f00a";
				$url2 = "http://whatsapi.smsinsta.com/api/send-text";

				// Testing Purpose
				// if($party->pty_comp_contact != ''){
				// 	$data2 = [
				// 		"number" => $party_contact,
				// 		"msg" => $whsbody,
				// 		"instance" => $ins,
				// 		"apikey" => $api
				// 	];
				// 	$ch = curl_init();
				// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
				// 	curl_setopt($ch, CURLOPT_URL, $url2);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	$result = curl_exec($ch);
				// 	curl_close($ch);
				// }

				// if($party->pty_contact_no != ''){
					
				// 	$data3 = [
				// 		"number" => $party_contact2,
				// 		"msg" => $whsbody,
				// 		"instance" => $ins,
				// 		"apikey" => $api
				// 	];
				// 	$ch = curl_init();
				// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data3));
				// 	curl_setopt($ch, CURLOPT_URL, $url2);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	$result = curl_exec($ch);
				// 	curl_close($ch);
				// }

		

				

			}

			// Store in candidate_list JOB
			$post = new CandidateListJpb();
			$post->cand_id = $cand_id;
			$post->pty_id = $cand_d->pty_id;
			$post->care_of_id = $care_of_id;
			$post->user_id = Auth::user()->user_id;
			$post->save();

			MofaPaidJob::dispatch($post,$cand_d,$party,$user,$pass_data,$getAPI)->onQueue('visaservice');

			// check Passport Type is ECR
			// $pass_type = Candidate::find($cand_id);
			// if($pass_type->cand_passport_type == 'ECR'){
			// 	// emigration is exists or not
			// 	$check_emig = Emigration::where('cand_id','=',$cand_id)->first();
			// 	if(isset($check_emig)){
			// 		// Update Emigration
			// 	}else{
			// 		$post_emig = new Emigration();
			// 		$post_emig->cand_id = $cand_id;
			// 		$post_emig->pass_no = $pass_type->cand_passport_no;
			// 		$post_emig->cand_name = $pass_type->cand_fname.' '.$pass_type->cand_lname;
			// 		$post_emig->pty_id = $pass_type->pty_id;
			// 		$post_emig->save();

			// 		// Insert Status
			// 		$last_emig_id = $post_emig->id;
			// 		$post_emig_st = new EmigrationStatus();
			// 		$post_emig_st->emig_id = $last_emig_id;
			// 		$post_emig_st->cand_id = $cand_id;
			// 		$post_emig_st->pty_id = $pass_type->pty_id;
			// 		$post_emig_st->status_name = 'New Documents';
			// 		$post_emig_st->save();
			// 	}
			// }

			


			Session::flash('success', 'Mofa unpaid status change successfully !');
			return redirect('services/candidate-status/' . $cand_id);

		}

		// $mofa_pay_name = $request->input('mofa_pay_name');
		// $cand_id = $request->input('cand_id');
		// for ($i=0; $i <count($mofa_pay_name) ; $i++) { 
		// 	$test = $request->input($mofa_pay_name[$i]);
		// 	if($test==0)
		// 	{
		// 	$mofa_status=0;
		// 		break;
		// 	}else{
		// 		$mofa_status=1;
		// 	}
		// }
	
		// $pass_data = $request->post();
		// // dd()
		// if ($mofa_status == 0) {
		// 	$ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
		// 	$ser_status->mofa_unpaid = 1;
		// 	$ser_status->mofa_paid = 0;
		// 	$ser_status->save();

		// 	date_default_timezone_set('Asia/Kolkata');
		// 	$timeline = new Timeline();
		// 	$timeline->cand_id = $cand_id;
		// 	$timeline->subject =  'Mofa Unpaid';
		// 	$timeline->time = date("h:i:sa");
		// 	$timeline->status = 4;
		// $timeline->user_id = Auth::user()->user_id;
		// 	$timeline->save();
		// }
		// if ($mofa_status == 1) {
		// 	$ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
		// 	$ser_status->mofa_paid = 1;
		// 	$ser_status->mofa_unpaid = 0;
		// 	$ser_status->save();

		// 	date_default_timezone_set('Asia/Kolkata');
		// 	$timeline = new Timeline();
		// 	$timeline->cand_id = $cand_id;
		// 	$timeline->subject =  'Mofa Paid';
		// 	$timeline->time = date("h:i:sa");
		// 	$timeline->status = 1;
		// $timeline->user_id = Auth::user()->user_id;
		// 	$timeline->save();
		// }

		// Session::flash('success', 'Mofa unpaid status change successfully !');
		// return redirect('services/candidate-status/' . $cand_id);
	}

	public function medicle_store(Request $request) {
        // dd($request);

		if ($request->hasFile('med_copy')) {
			$file = $request->file('med_copy');
			$file_count = File::files(base_path().'/public/status-images/medicle_copy');
			$filecount = 0;

			if ($file_count !== false) {
				$filecount = count($file_count);
			}
			$file_exe = $file->getClientOriginalExtension();
			$name = $filecount . '.' . $file_exe;

			$file->move(base_path().'/public/status-images/medicle_copy', $name);
			$medicle_copy = $name;

		} else {
			$medicle_copy = '';
		}

		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$health_st = $request->input('health_st');
		$med_online = $request->input('med_online');
		$med_id = $request->input('med_id');
		$med_service = $request->input('med_service');
		$medSerPay = $request->input('medSerPay');
		$med_report = $request->input('med_report');
		$med_rep_exp_date = $request->input('med_exp_date');
		$pass_data = array(
			'health_st' => $health_st,
			'med_online' => $med_online,
			'medicle_copy' => $medicle_copy,
			'med_id' => $med_id,
			'created_at' =>  Carbon::now()->toDateTimeString()
		);
		$pass_data = serialize($pass_data);

		// $sd = ServiceDetails::where('cand_passport_no', '=', $pass_no)->first();
		$sd = ServiceDetails::where('cand_id', '=', $cand_id)->first();
		$sd->medicle = $pass_data;
		$sd->medical_report = $med_report;
		$sd->med_service = $med_service;
		$sd->medSerPay = $medSerPay;
		$sd->med_rep_exp_date = $med_rep_exp_date;
		$sd->save();

		// Candidate Status
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();

		$cand_nst = ComplCandStatus::where('cand_id','=',$cand_id)->first();
		 

		// Main Status
		$finalStatus = new CandCompFinalStatus();

		// Update in Candidate Status New
		// $cand_st_new = CandStatusNew::where('cand_id','=',$cand_id)->first();

		// Update in Stage and Status
		// $stage_f = CandStageF::where('cand_id','=',$cand_id)->first();
		// $status_f = CandCstatusF::where('cand_id','=',$cand_id)->first();
		// $incom_f = CandIncomF::where('cand_id','=',$cand_id)->first();

		// if Healt Status is 1
		if($health_st == 1){
				
			// check Medical Unfit, Permanently Unfit and Medical Service
			$chk_mun = CandCompFinalStatus::where('final_status','=','Medical Unfit')->where('cand_id','=',$cand_id)->first();
			if(isset($chk_mun)){
				$chk_mun->delete();
			}
			$chk_ms = CandCompFinalStatus::where('final_status','=','Medical Service')->where('cand_id','=',$cand_id)->first();
			if(isset($chk_ms)){
				$chk_ms->delete();
			}
			$chk_pun = CandCompFinalStatus::where('final_status','=','Permanently Unfit')->where('cand_id','=',$cand_id)->first();
			if(isset($chk_pun)){
				$chk_pun->delete();
			}


			// Check Medical Fit is exist
			$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
			if($chk_mf == ''){
				$finalStatus->cand_id = $cand_id;
				$finalStatus->final_status = "Medical Fit";
				$finalStatus->save();
			}

			// Update Status when only select Fit
			if($med_online == '' & $med_report == ''){
				// Update Candidate Status
				$cand_status->medicalfit = 1;
				$cand_status->medicalunfit = 0;
				$cand_status->mediicalservice = 0;
				$cand_status->permanentlyunfit = 0;
				$cand_status->medicalonline = 0;
				$cand_status->medicalnotonline = 0;
				$cand_status->waiting_for_medical_report = 0;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = 'Medical Fit';
				$cand_status->incomplete_status = '';

				$cs_status = array($cand_nst->c_status,"Medical Fit");

				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->MEDICAL_FIT = 1;
				$cand_nst->save();

				
				// $status_f->med_fit = 1;
				// $status_f->med_online = 0;
				// $status_f->med_rep_rec = 0;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				

				// $incom_f->medical_unfit = 0;
				// $incom_f->medical_not_online = 0;
				// $incom_f->waiting_for_medical_rep = 0;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();


				// Check Medical Fit is exist
				$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();

				if($chk_mf == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Fit";
					$finalStatus->save();
				}

				

				// Update New Status Candidate
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = '';
				// $cand_st_new->stc_status = 'Medical Fit';
				// $cand_st_new->save();

			}
			// Update Status when click Online
			if($med_online==1 && $med_online !='' && $med_report == ''){
				$cand_status->medicalonline = 1;
				$cand_status->medicalnotonline = 0;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = 'Medical Online';
				$cand_status->incomplete_status = '';



				$cs_status = array($cand_nst->c_status,"Medical Online","Medical Fit");

				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->MEDICAL_ONLINE = 1;
				$cand_nst->save();

				// Check Medical Fit is exist
				$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
				if($chk_mf == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Fit";
					$finalStatus->save();
				}

				// Check Medical online is exist
				$chk_mo = CandCompFinalStatus::where('final_status','=','Medical Online')->where('cand_id','=',$cand_id)->first();
				if($chk_mo == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Online";
					$finalStatus->save();
				}


			

				// $status_f->med_fit = 1;
				// $status_f->med_online = 1;
				// $status_f->med_rep_rec = 0;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->medical_unfit = 0;
				// $incom_f->medical_not_online = 0;
				// $incom_f->waiting_for_medical_rep = 0;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

				// // Update New Status Candidate
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = '';
				// $cand_st_new->stc_status = 'Medical Online';
				// $cand_st_new->save();
			}
			
			// Update Status when click on Pending
			if($med_online==0 && $med_online !='' && $med_report == ''){
				
				$cand_status->medicalonline = 0;
				$cand_status->medicalnotonline = 1;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = '';
				$cand_status->incomplete_status = 'Medical Not Online';




				// final status
				$cs_status = array($cand_nst->c_status,"Medical Fit");
				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->save();


				// Check Medical Fit is exist
				$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
				if($chk_mf == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Fit";
					$finalStatus->save();
				}


				// Check Medical Online is exist to delete
				$chk_mo = CandCompFinalStatus::where('final_status','=','Medical Online')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_mo)){
					$chk_mo->delete();
				}

				
				// $status_f->med_fit = 1;
				// $status_f->med_online = 0;
				// $status_f->med_rep_rec = 0;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->medical_unfit = 0;
				// $incom_f->medical_not_online = 1;
				// $incom_f->waiting_for_medical_rep = 0;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();


				// Update New Status Candidate
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = 'MEDICAL NOT ONLINE';
				// $cand_st_new->stc_status = '';
				// $cand_st_new->save();

			}
			
			if($med_report == 'No' && $med_report != '' && $med_online == ''){
				$cand_status->waiting_for_medical_report = 1;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = '';
				$cand_status->incomplete_status = 'Waiting for medical report';

				$cs_status = array($cand_nst->c_status,"Medical Fit");
				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->save();

				// Check Medical Fit is exist
				$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
				if($chk_mf == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Fit";
					$finalStatus->save();
				}

				// Check if Medical Report Received exist it should be delete
				$chk_mr = CandCompFinalStatus::where('final_status','=','Medical Report Received')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_mr)){
					$chk_mr->delete();
				}

				// $status_f->med_fit = 1;
				// $status_f->med_online = 0;
				// $status_f->med_rep_rec = 0;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->medical_unfit = 0;
				// $incom_f->medical_not_online = 0;
				// $incom_f->waiting_for_medical_rep = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

				// Update New Status Candidate
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = 'Waiting for medical report';
				// $cand_st_new->stc_status = '';
				// $cand_st_new->save();
			}
			if($med_report == 'Yes' && $med_report != '' && $med_online == ''){
				$cand_status->waiting_for_medical_report = 0;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = 'Medical Report Received';
				$cand_status->incomplete_status = '';


				// Update in Service Details


				$cs_status = array($cand_nst->c_status,"Medical Report Received,Medical Fit");

				$cand_nst->c_status = implode(",",$cs_status);

				$cand_nst->Medical_Report_Received = 1;
				$cand_nst->save();

				// Check Medical Fit is exist
				$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
				if($chk_mf == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Fit";
					$finalStatus->save();
				}

				// Check if Medical Report Receieved exist or not
				$chk_mr = CandCompFinalStatus::where('final_status','=','Medical Report Received')->where('cand_id','=',$cand_id)->first();
				if($chk_mr == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Report Received";
					$finalStatus->save();
				}
				// Update New Status Candidate
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = '';
				// $cand_st_new->stc_status = 'Medical Report Received';
				// $cand_st_new->save();


				// $status_f->med_fit = 1;
				// $status_f->med_online = 0;
				// $status_f->med_rep_rec = 1;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->medical_unfit = 0;
				// $incom_f->medical_not_online = 0;
				// $incom_f->waiting_for_medical_rep = 0;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

			}

			// Medical Not Online and Report Receive
			if($med_online == 0 && $med_report == 'No'){
				$cand_status->medicalonline = 0;
				$cand_status->medicalnotonline = 1;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = '';


				// $incom_st = array("Medical Not Online,Waiting for medical report");

				
				$cand_status->incomplete_status = "Medical Not Online,Waiting for medical report";
				$cand_status->save();
			
				$cs_status = array($cand_nst->c_status,"Medical Fit");
				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->save();

				// Check Medical Fit is exist
				$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
				if($chk_mf == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Fit";
					$finalStatus->save();
				}

				// check Medical Report exist in database
				$chk_mr = CandCompFinalStatus::where('final_status','=','Medical Report Received')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_mr)){
					$chk_mr->delete();
				}

				// Check Medical online is exist
				$chk_mo = CandCompFinalStatus::where('final_status','=','Medical Online')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_mo)){
					$chk_mo->delete();
				}

				// Update New Status Candidate
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = 'Waiting for medical report';
				// $cand_st_new->stc_status = '';
				// $cand_st_new->save();

				// $status_f->med_fit = 1;
				// $status_f->med_online = 0;
				// $status_f->med_rep_rec = 0;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->medical_unfit = 0;
				// $incom_f->medical_not_online = 1;
				// $incom_f->waiting_for_medical_rep = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

			}

			// Medical Online and Report Receive
			if($med_online == 1 && $med_report == 'No'){
				$cand_status->medicalonline = 0;
				$cand_status->medicalnotonline = 1;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = '';
				$cand_status->incomplete_status = 'Waiting for medical report';

				$cs_status = array($cand_nst->c_status,"Medical Fit","Medical Online");
				$cand_nst->c_status = implode(",",$cs_status);
				$cand_nst->save();

				// Check Medical Fit is exist
				$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
				if($chk_mf == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Fit";
					$finalStatus->save();
				}

				// Check Medical online is exist
				$chk_mo = CandCompFinalStatus::where('final_status','=','Medical Online')->where('cand_id','=',$cand_id)->first();
				if($chk_mo == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Online";
					$finalStatus->save();
				}

				// Check if Medical Report Receieved exist or not
				$chk_mr = CandCompFinalStatus::where('final_status','=','Medical Report Received')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_mr)){
					$chk_mr->delete();
				}

				// Update New Status Candidate
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = 'Waiting for medical report';
				// $cand_st_new->stc_status = '';
				// $cand_st_new->save();

				// $status_f->med_fit = 1;
				// $status_f->med_online = 1;
				// $status_f->med_rep_rec = 0;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->medical_unfit = 0;
				// $incom_f->medical_not_online = 0;
				// $incom_f->waiting_for_medical_rep = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

			}

			

			// Medical Status
			if($med_online==1 && $med_report == 'Yes'){
				// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
				$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
				$ser_status->medicle = 1;
				$ser_status->medicle_online_required = 0;
				$ser_status->save();

				// Update Status
				$cand_status->medicalonline = 0;
				$cand_status->medicalnotonline = 1;
				$cand_status->active_status = 'Waiting For Visa Number';
				$cand_status->active_sub_status = 'Medical Online';
				$cand_status->incomplete_status = '';

				$cs_status = array($cand_nst->c_status,"Medical Online","Medical Report Received","Medical Fit");

				$cand_nst->c_status = implode(",",$cs_status);

				$cand_nst->MEDICAL_ONLINE = 1;
				$cand_nst->save();

				// Check Medical Fit is exist
				$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
				if($chk_mf == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Fit";
					$finalStatus->save();
				}

				// Check Medical online is exist
				$chk_mo = CandCompFinalStatus::where('final_status','=','Medical Online')->where('cand_id','=',$cand_id)->first();
				if($chk_mo == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Online";
					$finalStatus->save();
				}

				// Check if Medical Report Receieved exist or not
				$chk_mr = CandCompFinalStatus::where('final_status','=','Medical Report Received')->where('cand_id','=',$cand_id)->first();
				if($chk_mr == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Report Received";
					$finalStatus->save();
				}

				// Update New Status Candidate
				// $cand_st_new->stage = 'Waiting For Visa Number';
				// $cand_st_new->st_pending = '';
				// $cand_st_new->stc_status = 'Medical Online';
				// $cand_st_new->save();
	
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject =  'Medicle Online Update Done';
				$timeline->time = date("h:i:sa");
				$timeline->status = 2;
				$timeline->user_id = Auth::user()->user_id;
				$timeline->save();

				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 0;
				// $stage_f->waiting_for_med_fit = 0;
				// $stage_f->waiting_for_visa = 1;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();


				// $status_f->med_fit = 1;
				// $status_f->med_online = 1;
				// $status_f->med_rep_rec = 1;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->medical_unfit = 0;
				// $incom_f->medical_not_online = 0;
				// $incom_f->waiting_for_medical_rep = 0;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();
	
			}
			
			if($med_online==0 && $med_report == 'Yes'){
				// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
				$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
				$ser_status->medicle = 0;
				$ser_status->medicle_online_required = 0;
				$ser_status->medicle_online_update = 0;
				$ser_status->save();

				// Update 
				$cand_status->medicalonline = 0;
				$cand_status->medicalnotonline = 1;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = '';
				$cand_status->incomplete_status = 'Medical Not Online';

				$cs_status = array($cand_nst->c_status,"Medical Report Received","Medical Fit");

				$cand_nst->c_status = implode(",",$cs_status);

				
				$cand_nst->save();

				$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
				if($chk_mf == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Fit";
					$finalStatus->save();
				}

				// Check Medical online is exist
				$chk_mo = CandCompFinalStatus::where('final_status','=','Medical Online')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_mo)){
					$chk_mo->delete();
				}

				// Check if Medical Report Receieved exist or not
				$chk_mr = CandCompFinalStatus::where('final_status','=','Medical Report Received')->where('cand_id','=',$cand_id)->first();
				if($chk_mr == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Report Received";
					$finalStatus->save();
				}

				// $status_f->med_fit = 1;
				// $status_f->med_online = 0;
				// $status_f->med_rep_rec = 1;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->medical_unfit = 0;
				// $incom_f->medical_not_online = 1;
				// $incom_f->waiting_for_medical_rep = 0;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

				// Update New Status Candidate
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = 'Medical Not Online';
				// $cand_st_new->stc_status = '';
				// $cand_st_new->save();
	
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject =  'Medicle Online Update Required';
				$timeline->time = date("h:i:sa");
				$timeline->status = 2;
				$timeline->user_id = Auth::user()->user_id;
				$timeline->save();

				Session::flash('success', 'Medicle Updated successfully !');
				return redirect('services/candidate-status/' . $cand_id);
	
			}

			// Medical Report
			
			// if($med_report == 'No'){
			// 	$ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			// 	$ser_status->medicle = 0;
			// 	$ser_status->save();

			// 	// Waiting for
			// 	$cand_status->active_status = 'Waiting For Medical Fitness';
			// 	$cand_status->active_sub_status = '';
			// 	$cand_status->incomplete_status = 'Waiting for medical report';
			// 	$cand_status->save();


			// 	$cs_status = array($cand_nst->c_status,"Medical Fit");

			// 	$cand_nst->c_status = implode(",",$cs_status);

				
			// 	$cand_nst->save();

			// 	date_default_timezone_set('Asia/Kolkata');
			// 	$timeline = new Timeline();
			// 	$timeline->cand_id = $cand_id;
			// 	$timeline->subject =  'Medicle Report Not received';
			// 	$timeline->time = date("h:i:sa");
			// 	$timeline->status = 2;
			// 	$timeline->user_id = Auth::user()->user_id;
			// 	$timeline->save();
				
			// 	Session::flash('success', 'Medical Report Not Received !');
			// 	return redirect('services/candidate-status/' . $cand_id);
				
			// }

			$cand_status->save();
			
			


		}

		if($health_st == 0){

			// Check Medical Fit is exist
			$chk_mf = CandCompFinalStatus::where('final_status','=','Medical Fit')->where('cand_id','=',$cand_id)->first();
			if(isset($chk_mf)){
				$chk_mf->delete();
			}

			// // Check Medical Unfit Exist or Not
			// $chk_mfu = CandCompFinalStatus::where('final_status','=','Medical Unfit')->where('cand_id','=',$cand_id)->first();
			// if($chk_mfu == null){
			// 	$finalStatus->cand_id = $cand_id;
			// 	$finalStatus->final_status = "Medical Unfit";
			// 	$finalStatus->save();
			// }

			// Update Status when only select unfit
			if($request->med_service ==''){
				$cand_status->medicalunfit = 1;
				$cand_status->medicalfit = 0;
				$cand_status->medicalnotonline = 0;
				$cand_status->medicalonline = 0;
				$cand_status->waiting_for_medical_report = 0;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = '';
				$cand_status->incomplete_status = 'Medical Unfit';

				// $status_f->med_fit = 0;
				// $status_f->med_online = 0;
				// $status_f->med_rep_rec = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 0;
				// $status_f->save();

				// $incom_f->waiting_for_medical_rep = 0;
				// $incom_f->medical_not_online = 0;
				// $incom_f->medical_unfit = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();


				// $cs_status = array($cand_nst->c_status,"Medical Unfit");
				// $cand_nst->c_status = implode(",",$cs_status);
				// $cand_nst->save();
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = 'Medical Unfit';
				// $cand_st_new->stc_status = '';
				// $cand_st_new->save();

			}


			// Medical is Service is 0
			if($health_st == 0 && $med_service == 0 && $med_service !=''){
				// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
				$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
				$ser_status->medicle = 1;
				$ser_status->medicle_online_required = 0;
				$ser_status->save();
	
				// Update Status
				$cand_status->mediicalservice = 1;
				$cand_status->active_status = 'Waiting For Visa Number';
				$cand_status->active_sub_status = 'Medical Service';
				$cand_status->incomplete_status = '';

				$cs_status = array($cand_nst->c_status,"Medical Service");

				$cand_nst->c_status = implode(",",$cs_status);

				$cand_nst->Medical_Service = 1;
				$cand_nst->save();


				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 0;
				// $stage_f->waiting_for_med_fit = 0;
				// $stage_f->waiting_for_visa = 1;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// $status_f->med_fit = 0;
				// $status_f->med_online = 0;
				// $status_f->med_rep_rec = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->medical_service = 1;
				// $status_f->perm_unfit = 0;
				// $status_f->save();

				

				// $incom_f->waiting_for_medical_rep = 0;
				// $incom_f->medical_not_online = 0;
				// $incom_f->medical_unfit = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

				// Check Medical Service Exist or Not
				$chk_ms = CandCompFinalStatus::where('final_status','=','Medical Service')->where('cand_id','=',$cand_id)->first();
				if($chk_ms == null){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Medical Service";
					$finalStatus->save();
				}
				// Check Permanenty Unfit is exist
				$chk_pun = CandCompFinalStatus::where('final_status','=','Permanently Unfit')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_pun)){
					$chk_pun->delete();
				}
				// Update New Status
				// $cand_st_new->stage = 'Waiting For Visa Number';
				// $cand_st_new->st_pending = '';
				// $cand_st_new->stc_status = 'Medical Service';
				// $cand_st_new->save();

				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject =  'Medicle Online Update Done';
				$timeline->time = date("h:i:sa");
				$timeline->status = 2;
				$timeline->user_id = Auth::user()->user_id;
				$timeline->save();
			}

			// Medical Service is 1
			if($health_st == 0 && $med_service == 1 && $med_service !=''){
				// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
				$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
				$ser_status->medicle = 0;
				$ser_status->medicle_online_required = 0;
				$ser_status->medicle_online_update = 0;
				$ser_status->save();

				// Update Status
				$cand_status->permanentlyunfit = 1;
				$cand_status->active_status = 'Waiting For Medical Fitness';
				$cand_status->active_sub_status = 'Permanently Unfit';
				$cand_status->incomplete_status = '';

				$cs_status = array($cand_nst->c_status,"Permanently Unfit");

				$cand_nst->c_status = implode(",",$cs_status);

				$cand_nst->Permanently_Unfit = 1;
				$cand_nst->save();

				// $status_f->med_fit = 0;
				// $status_f->med_online = 0;
				// $status_f->med_rep_rec = 0;
				// $status_f->medical_service = 0;
				// $status_f->perm_unfit = 1;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->waiting_for_medical_rep = 0;
				// $incom_f->medical_not_online = 0;
				// $incom_f->medical_unfit = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();

				// Check Permanenty Unfit is exist
				$chk_pun = CandCompFinalStatus::where('final_status','=','Permanently Unfit')->where('cand_id','=',$cand_id)->first();
				if($chk_pun == null){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Permanently Unfit";
					$finalStatus->save();
				}
				// Check Medical Service Exist or Not
				$chk_ms = CandCompFinalStatus::where('final_status','=','Medical Service')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_ms)){
					$chk_ms->delete();
				}
				



				// Update New Status
				// $cand_st_new->stage = 'Waiting For Medical Fitness';
				// $cand_st_new->st_pending = '';
				// $cand_st_new->stc_status = 'Permanently Unfit';
				// $cand_st_new->save();
	
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject =  'Medicle Online Update Required';
				$timeline->time = date("h:i:sa");
				$timeline->status = 2;
				$timeline->user_id = Auth::user()->user_id;
				$timeline->save();
			}

			$cand_status->save();

		}
		
		// Update Status Accordingly

		// check Passport Type is ECR
		// $pass_type = Candidate::find($cand_id);
		// if($pass_type->cand_passport_type == 'ECR'){
		// 	// emigration is exists or not
		// 	$check_emig = Emigration::where('cand_id','=',$cand_id)->first();
		// 	if(isset($check_emig)){
		// 		// Update Emigration
		// 	}else{
		// 		$post_emig = new Emigration();
		// 		$post_emig->cand_id = $cand_id;
		// 		$post_emig->pass_no = $pass_type->cand_passport_no;
		// 		$post_emig->cand_name = $pass_type->cand_fname.' '.$pass_type->cand_lname;
		// 		$post_emig->pty_id = $pass_type->pty_id;
		// 		$post_emig->save();

		// 		// Insert Status
		// 		$last_emig_id = $post_emig->id;
		// 		$post_emig_st = new EmigrationStatus();
		// 		$post_emig_st->emig_id = $last_emig_id;
		// 		$post_emig_st->cand_id = $cand_id;
		// 		$post_emig_st->pty_id = $pass_type->pty_id;
		// 		$post_emig_st->status_name = 'New Documents';
		// 		$post_emig_st->save();
		// 	}
		// }

		Session::flash('success', 'Medicle done successfully !');
		return redirect('services/candidate-status/' . $cand_id);
	}

	public function medicle_online_update_store(Request $request) {

		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$med_online = $request->input('med_online');

		if($med_online==1){

			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->medicle = 1;
			$ser_status->medicle_online_update = 0;
			$c = $ser_status->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Medicle Online Update Done';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();

		}

		Session::flash('success', 'Medicle online done successfully !');
		return redirect('services/candidate-status/' . $cand_id);
	}

	public function await_passport_store(Request $request)
	{
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$awaited_passport = $request->input('awaited_passport');

		// Candidate Status
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		// Update in Servoce Detailas
		// $ser_d = ServiceDetails::where('cand_passport_no','=',$pass_no)->first();
		$ser_d = ServiceDetails::where('cand_id','=',$cand_id)->first();

		if($awaited_passport==1){
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->awaited_passport = 1;

			$ser_status->save();

			$cand_status->waiting_for_og_passport = 1;
			$cand_status->save();

			// Update in Service Details
			$ser_d->awaited_passport = 1;
			$ser_d->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Passport Received At Head Office';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();

			Session::flash('success', 'Passport status change successfully !');
			return redirect('services/candidate-status/' . $cand_id);
		}else{

			// Update In Service Status
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->awaited_passport = 0;
			$ser_status->save();

			// Update in Service Details
			$ser_d->awaited_passport = 0;
			$ser_d->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Passport Not Received At Head Office';
			$timeline->time = date("h:i:sa");
			$timeline->status = 4;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Passport status change successfully !');
			return redirect('services/candidate-status/' . $cand_id);
		}

		Session::flash('success', 'Passport status change successfully !');
		return redirect('services/candidate-status/' . $cand_id);
	}

	public function ass_candidate_employer_store(Request $request)
	{
		$emp_id = $request->input('emp_id') ? $request->input('emp_id') : '';
		$cand_id = $request->input('cand_id') ? $request->input('cand_id') : '';
		$prof_id = $request->input('prof_id') ? $request->input('prof_id') : '';


		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		
		$cand_status->waiting_for_visa_number = 1;
		$cand_status->active_status = 'Ready For Documentation';
		$cand_status->active_sub_status = '';
		$cand_status->incomplete_status = 'Required Documents';
		$cand_status->save();


		// Update in Stage and Status
		// $stage_f = CandStageF::where('cand_id','=',$cand_id)->first();
		// $status_f = CandCstatusF::where('cand_id','=',$cand_id)->first();
		// $incom_f = CandIncomF::where('cand_id','=',$cand_id)->first();


		// $stage_f->new_document = 0;
		// $stage_f->waiting_for_musaned_reg = 0;
		// $stage_f->waiting_for_dele_from_another_office = 0;
		// $stage_f->waiting_for_mofa = 0;
		// $stage_f->mofa_unpaid = 0;
		// $stage_f->waiting_for_med_fit = 0;
		// $stage_f->waiting_for_pass = 0;
		// $stage_f->waiting_for_visa = 0;
		// $stage_f->ready_for_docu = 1;
		// $stage_f->user_id = Auth::user()->user_id;
		// $stage_f->save();

		// $incom_f->req_docs = 1;
		// $incom_f->medical_unfit = 0;
		// $incom_f->user_id = Auth::user()->user_id;
		// $incom_f->save();

		// Update in Candidate Status New
		// $cand_st_new = CandStatusNew::where('cand_id','=',$cand_id)->first();
		// Update New Candidate Status
		// $cand_st_new->stage = 'Ready For Documentation';
		// $cand_st_new->st_pending = 'Required Documents';
		// $cand_st_new->stc_status = '';
		// $cand_st_new->save();

		if(($emp_id))
		{
			$e_data = Employee::find($emp_id);
			$c_data = Candidate::find($cand_id);
			$emp = new EmployeeCandidate();
			$emp->cand_id = $c_data->cand_id;  
			$emp->emp_id = $e_data->emp_id; 
			$emp->emp_file_no = $e_data->emp_file_no;
			$emp->cand_profession = $prof_id;
			$emp->user_ass_id = Auth::user()->user_id;
			$emp->save();

	
			Session::flash('success', 'Employer assign successfully !');

		}

		$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
		$ser_status->ass_candidate_to_employer = 1;
		$ser_status->save();

		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_id;
		$timeline->subject =  'Candidate Assign to Employer';
		$timeline->time = date("h:i:sa");
		$timeline->status = 1;
		$timeline->user_id = Auth::user()->user_id;
		$timeline->save();

		
		// check Passport Type is ECR
		$pass_type = Candidate::find($cand_id);
		if($pass_type->cand_passport_type == 'ECR'){
			// emigration is exists or not
			$check_emig = Emigration::where('cand_id','=',$cand_id)->first();
			if(isset($check_emig)){
				// Update Emigration
				$check_emig->emp_id = $emp_id;
				$check_emig->emp_cand_id = $emp->emp_cand_id;
				$check_emig->user_id = Auth::user()->user_id;
				$check_emig->careoff_id = $pass_type->care_of_id;
				$check_emig->save();
			}else{
				$post_emig = new Emigration();
				$post_emig->cand_id = $cand_id;
				$post_emig->pass_no = $pass_type->cand_passport_no;
				$post_emig->emp_id = $emp_id;
				$post_emig->emp_cand_id = $emp->emp_cand_id;
				$post_emig->cand_name = $pass_type->cand_fname.' '.$pass_type->cand_lname;
				$post_emig->pty_id = $pass_type->pty_id;
				$post_emig->user_id = Auth::user()->user_id;
				$post_emig->careoff_id = $pass_type->care_of_id;
				$post_emig->save();

				// Insert Status
				$last_emig_id = $post_emig->id;
				$post_emig_st = new EmigrationStatus();
				$post_emig_st->emig_id = $last_emig_id;
				$post_emig_st->cand_id = $cand_id;
				$post_emig_st->pty_id = $pass_type->pty_id;
				$post_emig_st->status_name = 'New Documents';
				$post_emig->user_id = Auth::user()->user_id;
				$post_emig_st->save();
			}
		}


		Session::flash('success', 'Status updated successfully !');
		return redirect('services/candidate-status/' . $cand_id);
	}

	public function ready_for_submission(Request $request){
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$ready_for_documentation = $request->input('ready_for_documentation');

		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		$ser_details = ServiceDetails::where('cand_id','=',$cand_id)->first();
		$cand_det = Candidate::where('cand_id','=',$cand_id)->first();
		$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();

		if($ready_for_documentation == 'submi_con'){
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->submission = 1;
			$ser_status->save();

			// update in service details
			$ser_details->submission_date = $request->submission_date;
			$ser_details->save();


			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->required_docs = 0;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 1;
			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;

			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 0;
			
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// Update Candidate Staus
			$cand_status->ready_for_submission = 0;
			$cand_status->req_docu = 0;
			$cand_status->submitted_consulate = 1;
			$cand_status->ready_for_documentation = 1;
			$cand_status->active_status = 'Submitted in Consulate';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = '';
			$cand_status->save();

			// Update New Status Candidate
			// $cand_st_new->stage = 'Submitted in Consulate';
			// $cand_st_new->st_pending = '';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Submitted in Consulate';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			// Created JOB
			$post = new CandidateListJpb();
			$post->cand_id = $cand_id;
			$post->pty_id = $cand_det->pty_id;
			$post->care_of_id = $cand_det->care_of_id;
			$post->user_id = Auth::user()->user_id;
			$post->musaned_date = $request->submission_date;
			$post->save();

			// Party Details
			$party = Party::where('pty_id','=',$cand_det->pty_id)->first();

			// Visa Detail
			$visa_g = EmployeeCandidate::where('cand_id','=',$request->input('cand_id'))->first();
			$visa_d = Employee::where('emp_id','=',$visa_g->emp_id)->first();

			VisaSubConsulated::dispatch($post,$party,$cand_det,$visa_d)->onQueue('visaservice');


			Session::flash('success', 'Ready for submit successfully !');
			return redirect('services/candidate-status/' . $cand_id);
		}

		if($ready_for_documentation == 'ready_s'){
			
			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			// Candidate Status
			$cand_status->ready_for_submission = 1;
			$cand_status->submitted_consulate = 0;
			$cand_status->req_docu = 0;
			$cand_status->ready_for_documentation = 1;


			$cand_status->active_status = 'Passport Ready For Submission';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = '';

			$cand_status->save();

			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->required_docs = 0;
			// $stage_f->pass_ready_for_submi = 1;
			// $stage_f->submitted_cons = 0;

			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;

			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 0;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// Update in New Candidate Status
			// $cand_st_new->stage = 'Passport Ready For Submission';
			// $cand_st_new->st_pending = '';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Ready For Submission';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Ready for Ready For Submission !');
			return redirect('services/candidate-status/' . $cand_id);
		}

		if($ready_for_documentation == 'req_d'){
			
			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			// Candidate Status
			$cand_status->ready_for_submission = 0;
			$cand_status->req_docu = 1;
			$cand_status->submitted_consulate = 0;
			$cand_status->ready_for_documentation = 1;
			$cand_status->active_status = 'Required Documents';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Required Documents';

			$cand_status->save();

			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->required_docs = 1;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 0;
			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;
			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 1;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// $cand_st_new->stage = 'Required Documents';
			// $cand_st_new->st_pending = 'Required Documents';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Required Documents';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Ready for Ready For Submission !');
			return redirect('services/candidate-status/' . $cand_id);
		}
		if($ready_for_documentation == 'sd0'){
			
			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			// Candidate Status
			$cand_status->ready_for_submission = 0;
			$cand_status->req_docu = 0;
			$cand_status->submitted_consulate = 0;
			$cand_status->ready_for_documentation = 1;

			$cand_status->active_status = 'Ready for Documentation';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Required Documents';

			$cand_status->save();

			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 1;
			// $stage_f->required_docs = 0;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 0;
			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;
			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 1;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// $cand_st_new->stage = 'Ready for Documentation';
			// $cand_st_new->st_pending = '';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Ready for Documentation';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Ready for Ready For Submission !');
			return redirect('services/candidate-status/' . $cand_id);
		}

	}

	public function ready_for_submission2(Request $request){
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$ready_for_documentation = $request->input('ready_for_documentation');

		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		$ser_details = ServiceDetails::where('cand_id','=',$cand_id)->first();
		$cand_det = Candidate::where('cand_id','=',$cand_id)->first();
		$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();

		// if($ready_for_documentation == 'submi_con'){
		// 	$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
		// 	$ser_status->submission = 1;
		// 	$ser_status->save();

		// 	// update in service details
		// 	$ser_details->submission_date = $request->submission_date;
		// 	$ser_details->save();


		// 	// Update Candidate Staus
		// 	$cand_status->ready_for_submission = 0;
		// 	$cand_status->req_docu = 0;
		// 	$cand_status->submitted_consulate = 1;
		// 	$cand_status->ready_for_documentation = 1;
		// 	$cand_status->active_status = 'Submitted in Consulate';
		// 	$cand_status->active_sub_status = '';
		// 	$cand_status->incomplete_status = '';
		// 	$cand_status->save();


		// 	// Update in Service Details
		// 	$ser_details->ready_for_documentation = $ready_for_documentation;
		// 	$ser_details->save();

		// 	date_default_timezone_set('Asia/Kolkata');
		// 	$timeline = new Timeline();
		// 	$timeline->cand_id = $cand_id;
		// 	$timeline->subject =  'Submitted in Consulate';
		// 	$timeline->time = date("h:i:sa");
		// 	$timeline->status = 1;
		// 	$timeline->user_id = Auth::user()->user_id;
		// 	$timeline->save();
			
		// 	// Created JOB
		// 	$post = new CandidateListJpb();
		// 	$post->cand_id = $cand_id;
		// 	$post->pty_id = $cand_det->pty_id;
		// 	$post->care_of_id = $cand_det->care_of_id;
		// 	$post->user_id = Auth::user()->user_id;
		// 	$post->musaned_date = $request->submission_date;
		// 	$post->save();

		// 	// Party Details
		// 	$party = Party::where('pty_id','=',$cand_det->pty_id)->first();

		// 	// Visa Detail
		// 	$visa_g = EmployeeCandidate::where('cand_id','=',$request->input('cand_id'))->first();
		// 	$visa_d = Employee::where('emp_id','=',$visa_g->emp_id)->first();

		// 	VisaSubConsulated::dispatch($post,$party,$cand_det,$visa_d)->onQueue('visaservice');


		// 	Session::flash('success', 'Ready for submit successfully !');
		// 	return redirect('services/candidate-status/' . $cand_id);
		// }

		if($ready_for_documentation == 'ready_s'){
			// Update in Service Status 05092023
			$ser_status->ready_for_documentation = 1;
			$ser_status->save();

			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			// Candidate Status
			$cand_status->ready_for_submission = 1;
			$cand_status->submitted_consulate = 0;
			$cand_status->req_docu = 0;
			$cand_status->ready_for_documentation = 1;


			$cand_status->active_status = 'Passport Ready For Submission';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = '';

			$cand_status->save();

			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->required_docs = 0;
			// $stage_f->pass_ready_for_submi = 1;
			// $stage_f->submitted_cons = 0;

			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;

			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 0;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// Update in New Candidate Status
			// $cand_st_new->stage = 'Passport Ready For Submission';
			// $cand_st_new->st_pending = '';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Ready For Submission';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Ready for Ready For Submission !');
			return redirect('services/candidate-status/' . $cand_id);
		}

		if($ready_for_documentation == 'req_d'){
			
			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			// Candidate Status
			$cand_status->ready_for_submission = 0;
			$cand_status->req_docu = 1;
			$cand_status->submitted_consulate = 0;
			$cand_status->ready_for_documentation = 1;
			$cand_status->active_status = 'Required Documents';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Required Documents';

			$cand_status->save();

			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->required_docs = 1;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 0;
			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;
			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 1;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// $cand_st_new->stage = 'Required Documents';
			// $cand_st_new->st_pending = 'Required Documents';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Required Documents';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Ready for Ready For Submission !');
			return redirect('services/candidate-status/' . $cand_id);
		}
		if($ready_for_documentation == 'sd0'){
			
			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			// Candidate Status
			$cand_status->ready_for_submission = 0;
			$cand_status->req_docu = 0;
			$cand_status->submitted_consulate = 0;
			$cand_status->ready_for_documentation = 1;

			$cand_status->active_status = 'Ready for Documentation';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Required Documents';

			$cand_status->save();

			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 1;
			// $stage_f->required_docs = 0;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 0;
			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;
			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 1;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// $cand_st_new->stage = 'Ready for Documentation';
			// $cand_st_new->st_pending = '';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Ready for Documentation';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Ready for Ready For Submission !');
			return redirect('services/candidate-status/' . $cand_id);
		}

	}

	public function ready_for_submit(Request $request){

		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$ready_for_documentation = $request->input('ready_for_documentation');

		

		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		// $ser_details = ServiceDetails::where('cand_passport_no','=',$pass_no)->first();
		$ser_details = ServiceDetails::where('cand_id','=',$cand_id)->first();
		$cand_det = Candidate::where('cand_id','=',$cand_id)->first();

		// Update in Candidate Status New
		// $cand_st_new = CandStatusNew::where('cand_id','=',$cand_id)->first();


		// Update in Stage and Status
		// $stage_f = CandStageF::where('cand_id','=',$cand_id)->first();
		// $status_f = CandCstatusF::where('cand_id','=',$cand_id)->first();
		// $incom_f = CandIncomF::where('cand_id','=',$cand_id)->first();

		if($ready_for_documentation == 'submi_con'){
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->submission = 1;
			$ser_status->save();

			// update in service details
			$ser_details->submission_date = $request->submission_date;
			$ser_details->save();


			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->required_docs = 0;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 1;
			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;

			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 0;
			
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// Update Candidate Staus
			$cand_status->ready_for_submission = 0;
			$cand_status->req_docu = 0;
			$cand_status->submitted_consulate = 1;
			$cand_status->ready_for_documentation = 1;
			$cand_status->active_status = 'Submitted in Consulate';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = '';
			$cand_status->save();

			// Update New Status Candidate
			// $cand_st_new->stage = 'Submitted in Consulate';
			// $cand_st_new->st_pending = '';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Submitted in Consulate';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			// Created JOB
			$post = new CandidateListJpb();
			$post->cand_id = $cand_id;
			$post->pty_id = $cand_det->pty_id;
			$post->care_of_id = $cand_det->care_of_id;
			$post->user_id = Auth::user()->user_id;
			$post->musaned_date = $request->submission_date;
			$post->save();

			// Party Details
			$party = Party::where('pty_id','=',$cand_det->pty_id)->first();

			// Visa Detail
			$visa_g = EmployeeCandidate::where('cand_id','=',$request->input('cand_id'))->first();
			$visa_d = Employee::where('emp_id','=',$visa_g->emp_id)->first();

			VisaSubConsulated::dispatch($post,$party,$cand_det,$visa_d)->onQueue('visaservice');


			Session::flash('success', 'Ready for submit successfully !');
			return redirect('services/candidate-status/' . $cand_id);
		}

		if($ready_for_documentation == 'ready_s'){
			
			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			// Candidate Status
			$cand_status->ready_for_submission = 1;
			$cand_status->submitted_consulate = 0;
			$cand_status->req_docu = 0;
			$cand_status->ready_for_documentation = 1;


			$cand_status->active_status = 'Passport Ready For Submission';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = '';

			$cand_status->save();

			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->required_docs = 0;
			// $stage_f->pass_ready_for_submi = 1;
			// $stage_f->submitted_cons = 0;

			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;

			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 0;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// Update in New Candidate Status
			// $cand_st_new->stage = 'Passport Ready For Submission';
			// $cand_st_new->st_pending = '';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Ready For Submission';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Ready for Ready For Submission !');
			return redirect('services/candidate-status/' . $cand_id);
		}

		if($ready_for_documentation == 'req_d'){
			
			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			// Candidate Status
			$cand_status->ready_for_submission = 0;
			$cand_status->req_docu = 1;
			$cand_status->submitted_consulate = 0;
			$cand_status->ready_for_documentation = 1;
			$cand_status->active_status = 'Required Documents';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Required Documents';

			$cand_status->save();

			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->required_docs = 1;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 0;
			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;
			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 1;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// $cand_st_new->stage = 'Required Documents';
			// $cand_st_new->st_pending = 'Required Documents';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Required Documents';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Ready for Ready For Submission !');
			return redirect('services/candidate-status/' . $cand_id);
		}
		if($ready_for_documentation == 'sd0'){
			
			// Update in Service Details
			$ser_details->ready_for_documentation = $ready_for_documentation;
			$ser_details->save();

			// Candidate Status
			$cand_status->ready_for_submission = 0;
			$cand_status->req_docu = 0;
			$cand_status->submitted_consulate = 0;
			$cand_status->ready_for_documentation = 1;

			$cand_status->active_status = 'Ready for Documentation';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Required Documents';

			$cand_status->save();

			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->ready_for_docu = 1;
			// $stage_f->required_docs = 0;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 0;
			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;
			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();


			// $incom_f->req_docs = 1;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();

			// $cand_st_new->stage = 'Ready for Documentation';
			// $cand_st_new->st_pending = '';
			// $cand_st_new->stc_status = '';
			// $cand_st_new->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Ready for Documentation';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
			
			Session::flash('success', 'Ready for Ready For Submission !');
			return redirect('services/candidate-status/' . $cand_id);
		}

		
	}
	public function hold(Request $request)
	{

		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
		$ser_status->hold = 1;
		$ser_status->submission = 1;
		$ser_status->save();
		Session::flash('success', 'Hold successfully !');
		return redirect('services/candidate-status/' . $cand_id);
        // return response()->json($cand_id);
	}
	public function unhold_submit(Request $request)
	{
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
		$ser_status->hold = 0;
		// $ser_status->submission = 0;
		$ser_status->save();
		// change status
		$holdc = Holdcand::where('cand_id','=',$cand_id)->first();
		if (isset($holdc)) {
			$holdc->status = '0';
			$holdc->save();
		}

		// change status
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		if ($ser_status->mofa_unpaid == 1 && $ser_status->mofa_paid == 0) {
			$cand_status->active_status = 'Mofa Upaid';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Mofa Unpaid';
			$cand_status->save();
		}elseif($ser_status->mofa_paid == 1 && $ser_status->medicle == 0){
			$cand_status->active_status = 'Waiting For Medical Fitness';
			$cand_status->active_sub_status = 'Mofa Paid';
			$cand_status->incomplete_status = '';
			$cand_status->save();
		}elseif($ser_status->medicle == 1 && $ser_status->ass_candidate_to_employer == 0){
			$cand_status->active_status = 'Waiting For Visa Number';
			$cand_status->active_sub_status = 'Medical Online';
			$cand_status->incomplete_status = '';
			$cand_status->save();
		}elseif($ser_status->ass_candidate_to_employer == 1 && $ser_status->submission == 0){
			$cand_status->active_status = 'Ready For Documentation';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = '';
			$cand_status->save();
		}

		Session::flash('success', 'Candidate release successfully!');
		return redirect('services/candidate-status/' . $cand_id);
	}

	public function submitted_store(Request $request){

		// dd($request);

		if ($request->hasFile('visa_copy')) {
			$file = $request->file('visa_copy');
			$file_count = File::files(base_path().'/public/status-images/visa_copy');
			$filecount = 0;

			if ($file_count !== false) {
				$filecount = count($file_count);
			}
			$file_exe = $file->getClientOriginalExtension();
			$name = $filecount . '.' . $file_exe;

			$file->move(base_path().'/public/status-images/visa_copy', $name);
			$visa_copy = $name;

		} else {
			$visa_copy = '';
		}

		$notes=  $request->input('notes');
		$emig_required =  $request->input('emig_required');
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$pass_recieved = $request->input('pass_recieved');
		$rej_reason = $request->input('reject_reason');
		// $expiry_date = $request->input('med_exp_date');

		$pass_data = array(
			'visa_copy' => $visa_copy,
			'notes' => $notes,
			'emig_required' => $emig_required,
			'pass_recieved' => $pass_recieved,
			'created_at' =>  Carbon::now()->toDateTimeString()
		);
		$pass_data = serialize($pass_data);

		// $sd = ServiceDetails::where('cand_passport_no', '=', $pass_no)->first();
		$sd = ServiceDetails::where('cand_id', '=', $cand_id)->first();
		$sd->visa_notes = $notes;
		$sd->submitted = $pass_data;

		$sd->pass_recieved = $request->pass_recieved;
		$sd->pass_copy_rec = $request->pass_copy_rec;

		// Canddiate Details
		$cand_det = Candidate::where('cand_id','=',$cand_id)->first();

		// Candidate Status
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();

		
		$cand_nst = ComplCandStatus::where('cand_id','=',$cand_id)->first();

		// Update in Candidate Status New
		// $cand_st_new = CandStatusNew::where('cand_id','=',$cand_id)->first();

		// Main Status
		$finalStatus = new CandCompFinalStatus();

		if($pass_recieved==1){
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			if($request->pass_copy_rec == 1){
				$ser_status->visa_stamp = 1;
				$ser_status->submitted = 1;
			}

			$cand_ser = CandidateService::where('cand_id', $cand_id)->first();
			$cand_cat = explode(",", $cand_ser->cand_service_category);

			if(!in_array('emig', $cand_cat) &&  in_array('air_ticket', $cand_cat)){
				$ser_status->flight_book_requied = 1;
			}

			$dnk = $ser_status->save();

			$sd->visa_stamped_date = $request->visa_st_date;

			$cand_status->visastamped = 1;
			$cand_status->visa_stamoed_rejected = 0;
			$cand_status->processcompleted = 1;
			$cand_status->active_status = 'Completed';
			$cand_status->active_sub_status = 'Visa Stamped';
			$cand_status->incomplete_status = '';
			$cand_status->save();

			$cs_status = array($cand_nst->c_status,"Visa Stamped");

			$cand_nst->c_status = implode(",",$cs_status);
			$cand_nst->Visa_Stamped = 1;
			$cand_nst->PROCCESS_COMPLETED = 1;
			$cand_nst->save();

			$chk_vis = CandCompFinalStatus::where('final_status','=','Visa Stamped')->where('cand_id','=',$cand_id)->first();

			if($chk_vis == ''){
				$finalStatus->cand_id = $cand_id;
				$finalStatus->final_status = "Visa Stamped";
				$finalStatus->save();
			}

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Visa Stampped';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();


			// Send Mail Notification to party
			$visa_g = EmployeeCandidate::where('cand_id','=',$request->input('cand_id'))->first();
			$visa_d = Employee::where('emp_id','=',$visa_g->emp_id)->first();
			// $party = Party::where('pty_id','=',$visa_d->pty_id)->first();
			$cand_d = Candidate::where('cand_id','=',$request->input('cand_id'))->first();
			$party = Party::where('pty_id','=',$cand_d->pty_id)->first();
			$candidate_name = $cand_d->cand_fname.' '.$cand_d->cand_mname.' '.$cand_d->cand_lname;
			$care_of_id = $cand_d->care_of_id;
			$pty_id = $cand_d->pty_id;
			$user = User::where('user_id','=',$care_of_id)->first();
			// Get Service API
			$getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();

			if(isset($user)){

				$party = Party::where('pty_id','=',$pty_id)->where('pty_id','!=',200)->first();
				if(isset($party)){
					$party_name = $party->pty_full_name;
					$party_ag_name = $party->pty_ag_name;
				}else{
					$party_name = '';
					$party_ag_name = '';
				}

				// Mail Details
				$mesSub = "Visa Stamped Successfully for ".$candidate_name." with ".$pass_no;
				$file_path = public_path('status-images/visa_copy');
				$filename = $file_path.'/'.$visa_copy;
				$data = [
					'name' => $candidate_name,
					'party_name' => $user->name,
					'agency' => $party->pty_ag_name,
					'passport' => $pass_no,
					'visa_no' => $visa_d->emp_visa_no,
					'visa_date' => date("d-m-Y",strtotime($request->visa_st_date)),
				];
				// Whatsapp Details
				$whsbody = "Dear ".$user->name."\n".$party_ag_name."\nThe Candidate ".$candidate_name." bearing passport No. ".$pass_no." with Visa number ".$visa_d->emp_visa_no." has been stamped successfully from Saudi Consulate on ".date("d-m-Y",strtotime($request->visa_st_date)).".";
				$ins = "f102f804a510c482fff09a29bf31e309902dbe276cb87844047d53797382d4be";
	    		$api = "717bc8c88c24fa3b19a2c923e402d1c9c09fa674282f22e30c7b67729641f00a";

				$url_path = url('/');
				$file_name = 'status-images/visa_copy';
				$filepath = $url_path.'/'.$file_name.'/'.$visa_copy;
				// $media = $filepath;

				$media = url('/status-images/visa_copy/'.$visa_copy);

				$ext = pathinfo($visa_copy,PATHINFO_EXTENSION);
					if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg'){
						$type = "image";
					}
					if($ext == 'pdf'){
						$type = "document";
					}

					if($ext == 'mp4'){
						$type = "video";
					}

				// $type = "image";

				$url4 = "http://whatsapi.smsinsta.com/api/send-media";
				$url5 = "http://whatsapi.smsinsta.com/api/send-text";

					if($user->user_id == '200'){
						$care_of_email ='';
						$care_of_mobile = '';
					}else{
						$care_of_email = $user->email;
						$care_of_mobile = '91'.$user->mobile;
					}

					// if($care_of_email != ''){
					// 	Mail::to($care_of_email)->send(new VisaCopyNotification3($filename,$mesSub,$data));
					// }

					// For Testing
					// if($user->mobile !=''){
					// 	// Send Media
					// 	$data6 = [
					// 		"number" => $care_of_mobile,
					// 		"msg" => '',
					// 		"media" => $media,
					// 		"type" => $type,
					// 		"instance" => $ins,
					// 		"apikey" => $api
					// 	];
		
					// 	$ch = curl_init();
					// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
					// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
					// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data6));
					// 	curl_setopt($ch, CURLOPT_URL, $url4);
					// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					// 	$result = curl_exec($ch);
					// 	curl_close($ch);

					// 	// Send Text
					// 	$data61 = [
					// 		"number" => $care_of_mobile,
					// 		"msg" => $whsbody,
					// 		// "media" => $media,
					// 		// "type" => $type,
					// 		"instance" => $ins,
					// 		"apikey" => $api
					// 	];
		
					// 	$ch = curl_init();
					// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
					// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
					// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data61));
					// 	curl_setopt($ch, CURLOPT_URL, $url5);
					// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					// 	$result = curl_exec($ch);
					// 	curl_close($ch);
					// }

			}
			

			if(isset($party)){
				$cand_d = Candidate::where('cand_id','=',$request->input('cand_id'))->first();
				$candidate_name = $cand_d->cand_fname.' '.$cand_d->cand_mname.' '.$cand_d->cand_lname;

				if($party->pty_id == '200'){
					// Secondary Partu Details
					$party_email = '';
					$party_contact = '';

					$party_email2 = '';
					$party_contact2 = '';
				}else{
					// Secondary Partu Details
					$party_email = $party->pty_email;
					$party_contact = '91'.$party->pty_comp_contact;

					$party_email2 = $party->sec_email;
					$party_contact2 = '91'.$party->pty_contact_no;
				}

				$mesSub = "Visa Stamped Successfully for ".$candidate_name." with ".$pass_no;
				$file_path = public_path('status-images/visa_copy');
				$filename = $file_path.'/'.$visa_copy;
				$data = [
					'name' => $candidate_name,
					'party_name' => $party->pty_full_name,
					'agency' => $party->pty_ag_name,
					'passport' => $pass_no,
					'visa_no' => $visa_d->emp_visa_no,
					'visa_date' => date("d-m-Y",strtotime($request->visa_st_date)),
				];

				// if($party_email != '' && $party_email2 !=''){
				// 	Mail::to($party_email)->cc($party_email2)->send(new VisaCopyNotification($filename,$mesSub,$data));
				// }

				// if($party_email != '' && $party_email2 ==''){
				// 	Mail::to($party_email)->send(new VisaCopyNotification2($filename,$mesSub,$data));
				// }

				// Send Whatsapo 
				$whsbody = "Dear ".$party->pty_full_name."\n".ucfirst($party->pty_ag_name)."\nThe Candidate ".$candidate_name." bearing passport No. ".$pass_no." with Visa number ".$visa_d->emp_visa_no." has been stamped successfully from Saudi Consulate on ".date("d-m-Y",strtotime($request->visa_st_date)).".";
				$ins = "f102f804a510c482fff09a29bf31e309902dbe276cb87844047d53797382d4be";
	    		$api = "717bc8c88c24fa3b19a2c923e402d1c9c09fa674282f22e30c7b67729641f00a";

				$url_path = url('/');
				$file_name = 'status-images/visa_copy';
				$filepath = $url_path.'/'.$file_name.'/'.$visa_copy;
				// $media = $filepath;

				$media = url('/status-images/visa_copy/'.$visa_copy);

				$ext = pathinfo($visa_copy,PATHINFO_EXTENSION);
				if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg'){
					$type = "image";
				}
				if($ext == 'pdf'){
					$type = "document";
				}

				if($ext == 'mp4'){
					$type = "video";
				}

				// $type = "image";

				$url4 = "http://whatsapi.smsinsta.com/api/send-media";
				$url5 = "http://whatsapi.smsinsta.com/api/send-text";

				// For Testing
				// if($party->pty_comp_contact != ''){
				// 	// Send Media
				// 	$data4 = [
				// 		"number" => $party_contact,
				// 		"msg" => '',
				// 		"media" => $media,
				// 		"type" => $type,
				// 		"instance" => $ins,
				// 		"apikey" => $api
				// 	];
	
				// 	$ch = curl_init();
				// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
				// 	curl_setopt($ch, CURLOPT_URL, $url4);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	$result = curl_exec($ch);
				// 	curl_close($ch);
				// 	// Send Text
				// 	$data41 = [
				// 		"number" => $party_contact,
				// 		"msg" => $whsbody,
				// 		// "media" => $media,
				// 		// "type" => $type,
				// 		"instance" => $ins,
				// 		"apikey" => $api
				// 	];
	
				// 	$ch = curl_init();
				// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data41));
				// 	curl_setopt($ch, CURLOPT_URL, $url5);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	$result = curl_exec($ch);
				// 	curl_close($ch);
				// }

				// if($party->pty_contact_no != ''){
				// 	$data5 = [
				// 		"number" => $party_contact2,
				// 		"msg" => $whsbody,
				// 		"media" => $media,
				// 		"type" => $type,
				// 		"instance" => $ins,
				// 		"apikey" => $api
				// 	];
	
				// 	$ch = curl_init();
				// 	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
				// 	curl_setopt($ch, CURLOPT_URL, $url4);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 	$result = curl_exec($ch);
				// 	curl_close($ch);
				// }


				// Care of ID
				// $user = User::where('user_id','=',$party->care_of_id)->where('user_id','!=',200)->first();
				// if(isset($user)){
				// 	$care_of_email = $user->email;
				// 	$care_of_mobile = '91'.$user->mobile;

				// 	if($care_of_email != ''){
				// 		Mail::to($care_of_email)->send(new VisaCopyNotification3($filename,$mesSub,$data));
				// 	}

				// 	if($care_of_mobile !=''){
				// 		$data6 = [
				// 			"number" => $care_of_mobile,
				// 			"msg" => $whsbody,
				// 			"media" => $media,
				// 			"type" => $type,
				// 			"instance" => $ins,
				// 			"apikey" => $api
				// 		];
		
				// 		$ch = curl_init();
				// 		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				// 		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// 		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data6));
				// 		curl_setopt($ch, CURLOPT_URL, $url4);
				// 		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// 		$result = curl_exec($ch);
				// 		curl_close($ch);
				// 	}

				// }


			}

			// Send Mail
			
			// Store in Candidate Job List
			$post = new CandidateListJpb();
			$post->cand_id = $cand_id;
			$post->pty_id = $pty_id;
			$post->care_of_id = $care_of_id;
			$post->user_id = Auth::user()->user_id;
			if($request->pass_copy_rec == 1){
				$post->file = $visa_copy;
				$ext2 = pathinfo($visa_copy,PATHINFO_EXTENSION);
				if($ext2 == 'jpg' || $ext2 == 'png' || $ext2 == 'jpeg'){
					$type = "image";
				}
				if($ext2 == 'pdf'){
					$type = "document";
				}
	
				if($ext2 == 'mp4'){
					$type = "video";
				}
				$post->file_ext = $type;
			}else{
				$post->file = '';
				$post->file_ext = '';
			}

			$post->send_type = 'registered';
			$post->musaned_date	=$request->visa_st_date;
			$post->save();

			VisaStampedJob::dispatch($post,$party,$user,$cand_d,$visa_d,$getAPI)->onQueue('visaservice');


			// check Passport Type is ECR
			$pass_type = Candidate::find($cand_id);
			if($pass_type->cand_passport_type == 'ECR'){
				// emigration is exists or not
				$check_emig = Emigration::where('cand_id','=',$cand_id)->first();
				if(isset($check_emig)){
					// Update Emigration
					$check_emig->emp_id = $visa_g->emp_id;
					$check_emig->emp_cand_id = $visa_g->emp_cand_id;
					$check_emig->user_id = Auth::user()->user_id;
					$check_emig->careoff_id = $pass_type->care_of_id;
					$check_emig->save();
				}else{
					$post_emig = new Emigration();
					$post_emig->cand_id = $cand_id;
					$post_emig->pass_no = $pass_type->cand_passport_no;
					$post_emig->emp_id =  $visa_g->emp_id;
					$post_emig->emp_cand_id = $visa_g->emp_cand_id;
					$post_emig->cand_name = $pass_type->cand_fname.' '.$pass_type->cand_lname;
					$post_emig->pty_id = $pass_type->pty_id;
					$post_emig->user_id = Auth::user()->user_id;
					$post_emig->careoff_id = $pass_type->care_of_id;
					$post_emig->save();
	
					// Insert Status
					$last_emig_id = $post_emig->id;
					$post_emig_st = new EmigrationStatus();
					$post_emig_st->emig_id = $last_emig_id;
					$post_emig_st->cand_id = $cand_id;
					$post_emig_st->pty_id = $pass_type->pty_id;
					$post_emig_st->status_name = 'New Documents';
					$post_emig_st->user_id = Auth::user()->user_id;
					$post_emig_st->save();
				}
			}


		}else if($pass_recieved==0){

			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			// $ser_status->rejected = 1;
			// $ser_status->submitted = ;
			$ser_status->rejected = 1;
			$ser_status->visa_stamp = 0;
			$ser_status->submitted = 0;
			$ser_status->reject_save_next = 0;
			$ser_status->save();

			// Update Status
			$cand_status->visastamped = 0;
			$cand_status->visa_stamoed_rejected = 1;

			$sd->reject_reason = $rej_reason;

			if($rej_reason == 1){
				$cand_status->active_status = 'Normal Rejection';
				$cand_status->active_sub_status = '';
				$cand_status->incomplete_status = 'Normal Rejection';
				$cand_status->save();


				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 0;
				// $stage_f->waiting_for_med_fit = 0;
				// $stage_f->waiting_for_pass = 0;
				// $stage_f->waiting_for_visa = 0;
				// $stage_f->hold = 0;
				// $stage_f->required_docs = 0;
				// $stage_f->ready_for_docu = 0;
				// $stage_f->pass_ready_for_submi = 0;
				// $stage_f->submitted_cons = 0;
				// $stage_f->visa_stamp_rejection = 0;
				// $stage_f->completed = 0;
				// $stage_f->ready_for_visa_can = 0;
				// $stage_f->sub_in_cons_for_visa_can = 0;
				// $stage_f->visa_canceled = 0;
				// $stage_f->normal_rej = 1;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// $status_f->visa_stamped = 0;
				// $status_f->visa_canceled = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->normal_rej = 1;
				// $incom_f->req_docs = 0;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();


				
			}			

			if($rej_reason == 2){
				$cand_status->active_status = 'Required Documents';
				$cand_status->active_sub_status = '';
				$cand_status->incomplete_status = 'Required Documents';
				$cand_status->save();

				$sd->docs_req = implode(",",$request->input('docs_req'));
				$sd->visa_notes = $request->input('notes');


				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 0;
				// $stage_f->waiting_for_med_fit = 0;
				// $stage_f->waiting_for_pass = 0;
				// $stage_f->waiting_for_visa = 0;
				// $stage_f->hold = 0;
				// $stage_f->required_docs = 1;
				// $stage_f->ready_for_docu = 0;
				// $stage_f->pass_ready_for_submi = 0;
				// $stage_f->submitted_cons = 0;
				// $stage_f->visa_stamp_rejection = 0;
				// $stage_f->completed = 0;
				// $stage_f->ready_for_visa_can = 0;
				// $stage_f->sub_in_cons_for_visa_can = 0;
				// $stage_f->visa_canceled = 0;
				// $stage_f->normal_rej = 0;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// $status_f->visa_stamped = 0;
				// $status_f->visa_canceled = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// $incom_f->normal_rej = 0;
				// $incom_f->req_docs = 1;
				// $incom_f->user_id = Auth::user()->user_id;
				// $incom_f->save();


			}

			

			$sd->visa_stamped_date = $request->visa_st_date;


			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Visa Rejected';
			$timeline->time = date("h:i:sa");
			$timeline->status = 5;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();

		}  

		if($emig_required==1){
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->emi_required = 1;
			$ser_status->save();
		}

		$sd->save();

		Session::flash('success', 'Submitted successfully !');
		return redirect('services/candidate-status/' . $cand_id);

	}

	public function reject_save_next(Request $request){

		// dd($request);

		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		
		$resubm_btn = $request->input('resubm_btn');
		// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
		$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		$ser_det = Servicedetails::where('cand_id','=',$cand_id)->first();


		// Update in Stage and Status
		// $stage_f = CandStageF::where('cand_id','=',$cand_id)->first();
		// $status_f = CandCstatusF::where('cand_id','=',$cand_id)->first();
		// $incom_f = CandIncomF::where('cand_id','=',$cand_id)->first();

		if($resubm_btn == 0){
			$ser_status->reject_save_next = 1;
			$ser_status->rejected = 0;
			$ser_status->submitted = 0;
			$ser_status->submission = 0;
			$cand_ser = CandidateService::where('cand_id', $cand_id)->first();
			$cand_cat = explode(",", $cand_ser->cand_service_category);
			if(!in_array('emig', $cand_cat) &&  in_array('air_ticket', $cand_cat)){
				$ser_status->flight_book_requied = 1;
			}
			$ser_status->save();

			// update in candidate status
			$cand_status->active_status = 'Resubmission';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Resubmission';
			$cand_status->save();

			// Update in Service Details
			$ser_det->resubm_btn = $resubm_btn;
			$ser_det->ready_for_documentation = "sd0";
			$ser_det->save();
			
			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->hold = 0;
			// $stage_f->required_docs = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 0;
			// $stage_f->visa_stamp_rejection = 0;
			// $stage_f->completed = 0;
			// $stage_f->ready_for_visa_can = 0;
			// $stage_f->sub_in_cons_for_visa_can = 0;
			// $stage_f->visa_canceled = 0;
			// $stage_f->resubmission = 1;
			// $stage_f->can_submission = 0;
			// $stage_f->normal_rej = 0;
			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();

			// $incom_f->req_docs = 0;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 1;
			// $incom_f->can_submission = 0;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();
		}

		if($resubm_btn == 1){
			// update in candidate status
			$cand_status->active_status = 'Canceled Submission';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Canceled Submission';
			$cand_status->save();


			// update in candidate status
			$cand_status->active_status = 'Resubmission';
			$cand_status->active_sub_status = '';
			$cand_status->incomplete_status = 'Resubmission';
			$cand_status->save();

			// Update in Service Details
			$ser_det->resubm_btn = $resubm_btn;
			$ser_det->ready_for_documentation = "sd0";
			$ser_det->save();
			
			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->hold = 0;
			// $stage_f->required_docs = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 0;
			// $stage_f->visa_stamp_rejection = 0;
			// $stage_f->completed = 0;
			// $stage_f->ready_for_visa_can = 0;
			// $stage_f->sub_in_cons_for_visa_can = 0;
			// $stage_f->visa_canceled = 0;
			// $stage_f->resubmission = 0;
			// $stage_f->can_submission = 1;
			// $stage_f->normal_rej = 0;
			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();

			// $incom_f->req_docs = 0;
			// $incom_f->normal_rej = 0;
			// $incom_f->resubmission = 0;
			// $incom_f->can_submission = 1;
			// $incom_f->user_id = Auth::user()->user_id;
			// $incom_f->save();


			// Update in Service Details
			$ser_det->resubm_btn = $resubm_btn;
			$ser_det->save();

			
		}


		
		
		
		// Session::flash('success', 'Hold successfully !');
		return redirect('services/candidate-status/' . $cand_id);
		
	
		
    
	}

	public function for_emig_store(Request $request){
        // dd($request);

		if ($request->hasFile('policy')) {
			$file = $request->file('policy');
			$file_count = File::files(base_path().'/public/status-images/policy');
			$filecount = 0;

			if ($file_count !== false) {
				$filecount = count($file_count);
			}
			$file_exe = $file->getClientOriginalExtension();
			$name = $filecount . '.' . $file_exe;

			$file->move(base_path().'/public/status-images/policy', $name);
			$policy = $name;

		} else {
			$policy = '';
		}


		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');

		$pass_data = array(
			'policy_no'=>  $request->input('policy_no'),
			'fe_no' =>  $request->input('fe_no'),
			'en_no' =>  $request->input('en_no'),
			'dm_no' =>  $request->input('dm_no'),
			'policy_img' =>  $policy,
			'created_at' =>  Carbon::now()->toDateTimeString()
		);
		$pass_data = serialize($pass_data);


		// $sd = ServiceDetails::where('cand_passport_no', '=', $pass_no)->first();
		$sd = ServiceDetails::where('cand_id', '=', $cand_id)->first();
		$sd->emi_apply = $pass_data;
		$sd->save();

		// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
		$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
		$ser_status->emi_apply = 1;
		$ser_status->save();

		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_id;
		$timeline->subject =  'Emigration Apply';
		$timeline->time = date("h:i:sa");
		$timeline->status = 2;
		$timeline->user_id = Auth::user()->user_id;
		$timeline->save();

		Session::flash('success', 'Emigration apply successfully !');
		return redirect('services/candidate-status/' . $cand_id);
	}

	public function in_emig_store(Request $request){

		if ($request->hasFile('emig_sticker')) {
			$file = $request->file('emig_sticker');
			$file_count = File::files('public/status-images/emigration_sticker');
			$filecount = 0;

			if ($file_count !== false) {
				$filecount = count($file_count);
			}
			$file_exe = $file->getClientOriginalExtension();
			$name = $filecount . '.' . $file_exe;

			$file->move('public/status-images/emigration_sticker', $name);
			$emig_sticker = $name;

		} else {
			$emig_sticker = '';
		}

		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$emig_status = $request->input('emig_status');
		$air_ticket =  $request->input('air_ticket');
		$pass_data = array(
			'emig_status'=>  $request->input('emig_status'),
			'notes' =>  $request->input('notes'),
			'air_ticket' =>  $request->input('air_ticket'),
			'emig_sticker' =>  $emig_sticker,
			'created_at' =>  Carbon::now()->toDateTimeString()

		);
		$pass_data = serialize($pass_data);
		if($emig_status == 1){
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->emi_done = 1;
			$ser_status->flight_book_requied = 1;
			$ser_status->emi_reject = 0;
			$ser_status->save();
			// $sd = ServiceDetails::where('cand_passport_no', '=', $pass_no)->first();
			$sd = ServiceDetails::where('cand_id', '=', $cand_id)->first();
			$sd->emi_done = $pass_data;
			$sd->save();

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Emigration Status Approved';
			$timeline->time = date("h:i:sa");
			$timeline->status = 1;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();

			Session::flash('success', 'Emigration done successfully !');


		}
		if($emig_status == 0){
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->emi_reject = 1;
			$ser_status->emi_done = 0;
			$ser_status->flight_book_requied = 0;

			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Emigration Status Rejected';
			$timeline->time = date("h:i:sa");
			$timeline->status = 5;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();

			$ser_status->save();
			Session::flash('error', 'Emigration reject successfully !');

		}



		return redirect('services/candidate-status/' . $cand_id);


	}

	public function air_ticket_store(Request $request){

		if ($request->hasFile('ticket')) {
			$file = $request->file('ticket');
			$file_count = File::files(base_path().'/public/status-images/air_ticket');
			$filecount = 0;

			if ($file_count !== false) {
				$filecount = count($file_count);
			}
			$file_exe = $file->getClientOriginalExtension();
			$name = $filecount . '.' . $file_exe;

			$file->move(base_path().'/public/status-images/air_ticket', $name);
			$ticket = $name;

		} else {
			$ticket = '';
		}
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');

		$pass_data = array(
			'pass_no' => $request->input('passport'),
			'cand_id' => $request->input('cand_id'),
			'book_date' => $request->input('book_date'),
			'ticket_no' =>  $request->input('ticket_no'),
			'purchase_from' => $request->input('purchase_from'),
			'ticket_where' =>  $request->input('ticket_where'),
			'pnr_no1' => $request->input('pnr_no1'),
			'pnr_no2' =>  $request->input('pnr_no2'),
			'notes' =>  $request->input('notes'),
			'from' =>  $request->input('from'),
			'via' =>  $request->input('via'),
			'to' =>  $request->input('to'),
			'ticket' =>  $ticket

		);
		$pass_data = serialize($pass_data);

		// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
		$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
		$ser_status->work_done = 1;
		$ser_status->save();
		// $sd = ServiceDetails::where('cand_passport_no', '=', $pass_no)->first();
		$sd = ServiceDetails::where('cand_id', '=', $cand_id)->first();
		$sd->flight_book = $pass_data;
		$sd->save();

		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_id;
		$timeline->subject =  'Flight Ticket Booked';
		$timeline->time = date("h:i:sa");
		$timeline->status = 1;
		$timeline->user_id = Auth::user()->user_id;
		$timeline->save();

		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_id;
		$timeline->subject =  'Work Done';
		$timeline->time = date("h:i:sa");
		$timeline->status = 1;
		$timeline->user_id = Auth::user()->user_id;
		$timeline->save();

		Session::flash('success', 'Flight book successfully !');
		return redirect('services/candidate-status/' . $cand_id);


	}

	public function emi_reject_next(Request $request){
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$resubm = $request->input('resubm');
		if($resubm=='1'){
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->emi_reject_next = 0;
			$ser_status->emi_reject = 0;
			$ser_status->emi_done = 0;
			$ser_status->emi_apply = 0;
			$ser_status->save();
			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Visa Resubmission';
			$timeline->time = date("h:i:sa");
			$timeline->status = 3;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
		}
		if($resubm=='0'){
			// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
			$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
			$ser_status->emi_reject_next = 1;
			$ser_status->emi_reject = 1;
			$ser_status->emi_done = 0;
			$ser_status->emi_apply = 0;
			$ser_status->flight_book_requied = 1;
			$ser_status->save();
			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject =  'Visa Resubmission Cancel';
			$timeline->time = date("h:i:sa");
			$timeline->status = 5;
			$timeline->user_id = Auth::user()->user_id;
			$timeline->save();
		}

   	 	// Session::flash('success', 'Hold successfully !');
		return redirect('services/candidate-status/' . $cand_id);
	}

	public function assign_to_hold(Request $request){
		$cand_id =  $request->input('cand_id');
		$hold_type =  $request->input('hold_type');
		$ser_st = ServiceStatus::where('cand_id','=',$cand_id)->first();
		$ser_st->hold= 1;
		$ser_st->save();

		// Store Details
		// check hold details store in Hold Table
		$checkhold = Holdcand::where('cand_id','=',$cand_id)->first();
		if (isset($checkhold)) {
			$checkhold->status = '1';
			$checkhold->user_id = Auth::user()->user_id;
			$checkhold->save();
		}else{
			$holdc = new Holdcand();
			$holdc->cand_id = $cand_id;
			$holdc->user_id = Auth::user()->user_id;
			$holdc->status = '1';
			$holdc->save();
		}

		// update status
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		$cand_status->active_status = 'Hold';
		$cand_status->active_sub_status = '';
		$cand_status->incomplete_status = 'Hold';
		$cand_status->save();

		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_id;
		$timeline->subject =  $hold_type.' '.'Hold';
		$timeline->time = date("h:i:sa");
		$timeline->status = 4;
		$timeline->user_id = Auth::user()->user_id;
		$timeline->save();
		Session::flash('success', 'Hold successfully !');
		return response()->json('1');

	}

	public function assign_to_cancel(Request $request){
		$cand_id =  $request->input('cand_id');
		$cancel_type =  $request->input('cancel_type');
		$ser_st = ServiceStatus::where('cand_id','=',$cand_id)->first();
		$ser_st->cancel= 1;
		$ser_st->work_done= 1; 
		$ser_st->save();

		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_id;
		$timeline->subject =  $cancel_type.' '.'Cancel';
		$timeline->time = date("h:i:sa");
		$timeline->status = 5;
		$timeline->user_id = Auth::user()->user_id;
		$timeline->save();

		Session::flash('error', 'Cancel successfully !');
		return response()->json('1');
	}

	public function ready_for_subm(Request $request)
	{
		$cand_id =  $request->input('cand_id');
		$ser_st = ServiceStatus::where('cand_id','=',$cand_id)->first();
		$ser_st->hold= 0; 
		$ser_st->save();

		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_id;
		$timeline->subject =  'Ready For Submission';
		$timeline->time = date("h:i:sa");
		$timeline->status = 1;
		$timeline->user_id = Auth::user()->user_id;
		$timeline->save();

		Session::flash('success', 'Ready for submission successfully !');
		return response()->json('1');
	}

	public function ready_for_resubm(Request $request)
	{
		$cand_id =  $request->input('cand_id');
		$ser_st = ServiceStatus::where('cand_id','=',$cand_id)->first();
		$ser_st->rejected= 0;
		$ser_st->submitted= 0; 
		$ser_st->save();
		Session::flash('success', 'Resubmitted successfully !');
		return response()->json('1');
	}

	public function get_service(Request $request)
	{
		$pass_type =  $request->input('pass_type');
		$allser = Service::all();
		$res='';
		$res.='<option value=""></option>';
		$res.='<option value="0">None</option>';
		if($pass_type == 'ECNR'){
			foreach ($allser as $ser ) {
				if($ser->type=='ecnr'){   
					 
					// $res.='<option value="0">None</option>';
					// $res.='<option value="3">Flight book</option>';
					
					$res.='<option value="'.$ser->ser_id.'">'.$ser->ser_name.'</option>';

				}
			}    
		}
		else if($pass_type == 'ECR'){
			foreach ($allser as $ser ) {
				// if($ser->type=='ecr' || $ser->type=='ecnr'){ 
					
				// 	$res.='<option value="'.$ser->ser_id.'">'.$ser->ser_name.'</option>';
				// }

				if ($ser->type == 'ecr') {
					$res.='<option value="'.$ser->ser_id.'">'.$ser->ser_name.'</option>';
				}
			}        
		}
		$arr['res'] = $res;
		return response()->json($arr);
	}

	public function cv_update(Request $request)
	{
		$cand = Candidate::find($request->input('cand_id'));
		$cand->cand_exo_location = $request->input('cand_exo_location') ? $request->input('cand_exo_location') : '';
		$cand->cand_position = $request->input('cand_position') ? $request->input('cand_position') : '';
		$cand->cand_perd = $request->input('cand_perd') ? $request->input('cand_perd') : '';
		$cand->cand_dr_age = $request->input('cand_dr_age') ? $request->input('cand_dr_age') : '';
		$cand->cand_ex_verified = $request->input('cand_ex_verified') ? $request->input('cand_ex_verified') : '';
		$cand->cand_country = $request->input('cand_country') ? $request->input('cand_country') : '';
		$cand->cand_salary = $request->input('cand_salary') ? $request->input('cand_salary') : '';
		$cand->cand_maritial = $request->input('cand_maritial') ? $request->input('cand_maritial') : '';
		$cand->cand_tobaco = $request->input('cand_tobaco') ? $request->input('cand_tobaco') : '';
		$cand->cand_gmap = $request->input('cand_gmap') ? $request->input('cand_gmap') : '';
		$cand->cand_smoking = $request->input('cand_smoking') ? $request->input('cand_smoking') : '';
		$cand->cand_behavior = $request->input('cand_behavior') ? $request->input('cand_behavior') : '';
		$cand->cand_lang = $request->input('cand_lang') ? $request->input('cand_lang') : '';
		$cand->cand_expr = $request->input('cand_expr') ? $request->input('cand_expr') : '';
		$cand->cand_car = $request->input('cand_car') ? $request->input('cand_car') : '';
		$cand->cand_sc = $request->input('cand_sc') ? $request->input('cand_sc') : '';
		$cand->cand_pre_location = $request->input('cand_pre_location') ? $request->input('cand_pre_location') : '';
		$cand->cand_co_person = $request->input('cand_co_person') ? $request->input('cand_co_person') : '';
		$cand->cand_email = $request->input('cand_email') ? $request->input('cand_email') : '';
		$cand->cand_fb_link = $request->input('cand_fb_link') ? $request->input('cand_fb_link') : '';
		$cand->cand_o_link = $request->input('cand_o_link') ? $request->input('cand_o_link') : '';
		$cand->cand_note = $request->input('cand_note') ? $request->input('cand_note') : '';
		$cand->save();

		Session::flash('success', 'Cv updated successfully !');
		return redirect()->back();
		// return redirect('services/candidate/view/indv/'.$request->input('cand_id'));
	}


	public function status()
	{
		$pageConfigs = ['pageHeader' => false];
		return view('content/service/status', [ 'pageConfigs' => $pageConfigs]);
	}

	public function status_by_passport(Request $request)
	{
		$pass_no = $request->input('pass_no');

		$data = Candidate::where('cand_passport_no','=',$pass_no)->first();
		if(isset($data)){
			return redirect('services/candidate-status/'.$data->cand_id);
		}else{
			Session::flash('error', 'Passport number not found !');
			return redirect('services/status');
		}

	}

	public function cand_viewnote_store(Request $request)
	{
		extract($_POST);
		$note = new CandidateViewNotes();
		$note->admin_id = '1';
		$note->staff_id = '1';
		$note->cand_id = $cand_id;
		$note->view_notes = $view_notes;
		$note->save();
		
		Session::flash('success', 'Notes added successfully !');
		return redirect('services/candidate/view/indv/'.$request->input('cand_id'));
	}

	public function cand_viewnote_update(Request $request)
	{
		extract($_POST);
		$note = CandidateViewNotes::find($edit_id);
		$note->view_notes = $view_notes;
		$note->update();
		
		Session::flash('success', 'Notes updated successfully !');
		return redirect()->back();
	}

	public function cand_viewnote_edit(Request $request)
	{
		extract($_POST);
		$note = CandidateViewNotes::find($edit_id);
		return response()->json($note);
	}

	public function cand_viewnote_delete(Request $request)
	{
		extract($_POST);
		$note = CandidateViewNotes::find($edit_id);
		$note->delete();
		Session::flash('success', 'Notes deleted successfully !');
		return redirect()->back();
	}

	public function health_status_update(Request $request)
	{
		
		$pass_data = array(
			'health_st' => $request->input('health_st'),
			'med_online' => $request->input('med_online'),
			'medicle_copy' => $request->input('medicle_copy'),
			'med_id' => $request->input('med_id')

		);
		$pass_data = serialize($pass_data);

		$sd = ServiceDetails::where('cand_id', '=', $request->input('cand_id'))->first();
		$sd->medicle = $pass_data;
		$sd->save();
	}

	public function medicle_online_update(Request $request)
	{
		
		$pass_data = array(
			'med_online' => $request->input('med_online')
		);
		$pass_data = serialize($pass_data);

		$sd = ServiceDetails::where('cand_id', '=', $request->input('cand_id'))->first();
		$sd->medicle_online_update = $pass_data;
		$sd->save();
	}

	public function document_list()
	{
		$pageConfigs = ['pageHeader' => false];
		return view('content/service/document-list', [ 'pageConfigs' => $pageConfigs]);
	}

	public function document_list_json(Request $request)
	{
		$data_cand = Candidate::select('cand_fname','cand_lname','cand_id','cand_passport_no','created_at','updated_at','cand_photo','cand_passport_doc','cand_cv','cand_doc')->orderBy('cand_id','desc')->where('cand_photo','=','')->orWhere('cand_passport_doc','=','')->orWhere('cand_cv','=','')->orWhere('cand_doc','=','')->get();
		return DataTables::of($data_cand)->toJson();
	}

	public function updateGoback(Request $request){
		$emig_st = EmigrationStatus::where('emig_id','=',$request->id)->first();
		$emig_st->already_exists = 0;
		$emig_st->status_name = $request->status;
		$emig_st->save();
	}

	public function emigration_list()
	{

		$newdocC = EmigrationStatus::where('status_name','=','New Documents')->count();
		$alxistC = EmigrationStatus::where('status_name','=','Already Exist')->count();
		$empdocrC = EmigrationStatus::where('status_name','=','Employer Docs Received')->count();
		$wfempdocC = EmigrationStatus::where('status_name','=','Waiting For Employer Docs')->count();
		$rfemprC = EmigrationStatus::where('status_name','=','Ready For Employer Registration')->count();
		$rfdmrC = EmigrationStatus::where('status_name','=','Ready For Demand Raised')->count();
		$rfjbaC = EmigrationStatus::where('status_name','=','Ready for job assign')->count();
		$wfcdocC = EmigrationStatus::where('status_name','=','Waiting for candidate document')->count();
		$rfemapC = EmigrationStatus::where('status_name','=','Ready for emigration apply')->count();
		$emappC = EmigrationStatus::where('status_name','=','Emigration applied')->count();
		$apprC = EmigrationStatus::where('status_name','=','Approved')->count();
		$rejC = EmigrationStatus::where('status_name','=','Reject')->count();

		$data = [
			'newdocC' => $newdocC,
			'alxistC' => $alxistC,
			'empdocrC' => $empdocrC,
			'wfempdocC' => $wfempdocC,
			'rfemprC' => $rfemprC,
			'rfdmrC' => $rfdmrC,
			'rfjbaC' => $rfjbaC,
			'wfcdocC' => $wfcdocC,
			'rfemapC' => $rfemapC,
			'emappC' => $emappC,
			'apprC' => $apprC,
			'rejC' => $rejC
		];

		$pageConfigs = ['pageHeader' => false];
		return view('content/service/emigration-list', [ 'pageConfigs' => $pageConfigs,'data' => $data]);
	}

	public function emigration_list_json2(){
		

		$data_cand = DB::table('emigrations as emigr')
			->join('emigration_statuses as emigst','emigst.cand_id','=','emigr.cand_id')
			->leftjoin('qr_candidate_tbl as cand','cand.cand_id','=','emigr.cand_id')
			->leftjoin('qr_employee_candidate as emp_cand','emp_cand.cand_id','=','emigr.cand_id')
			->leftjoin('qr_employee_tbl as emp','emp.emp_id','=','emp_cand.emp_id')
			->leftjoin('qr_party_tbl as pty','pty.pty_id','=','cand.pty_id')
			->select('emigr.*','cand.cand_fname as cfname','cand.cand_lname as clname','emp.emp_file_no as efile','emp.emp_visa_no as evisa','emp.emp_id_no as eid','emp.wakala_type as waktype','pty.pty_ag_name as agname','emigst.status_name as stname')
			->orderBy('emigr.id','DESC')
			->get();
		return DataTables::of($data_cand)->toJson();
		
	}

	public function empUpdate(Request $request,$id){
	
		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		// Get File and Upload it

		if($request->hasFile('national_id')){
			$file = $request->file('national_id');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$national_id = $name;
		}else{
			$national_id = $emig_post->national_id;
		}

		if($request->hasFile('sign')){
			$file = $request->file('sign');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$sign = $name;
		}else{
			$sign = $emig_post->sign;
		}

		if($request->hasFile('crcopy')){
			$file = $request->file('crcopy');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$crcopy = $name;
		}else{
			$crcopy = $emig_post->crcopy;
		}

		if($request->hasFile('stamp')){
			$file = $request->file('stamp');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			// $name = $filecount . '.' . $file_exe;
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$stamp = $name;
		}else{
			$stamp = $emig_post->stamp;
		}


		// Update in Emigration
		$emig_post->emp_name_eng = $request->emp_name_eng;
		$emig_post->emp_address = $request->emp_address;
		$emig_post->po_box_no = $request->po_box_no;
		$emig_post->contact_number = $request->contact_number;
		$emig_post->email = $request->email;
		$emig_post->emp_city = $request->emp_city;
		$emig_post->emp_pincode = $request->emp_pincode;
		$emig_post->emp_phone = $request->emp_phone;
		$emig_post->national_id = $national_id;
		$emig_post->pid = $request->pid;
		$emig_post->sign = $sign;
		$emig_post->crcopy = $crcopy;
		$emig_post->stamp = $stamp;
		// $emig_post->empdocup = $request->emp_docu;
		// $emig_post->wakala_type = $request->wakala_type;
		
		$emig_post->company_name = $request->company_name;
		$emig_post->comp_reg_no = $request->comp_reg_no;
		
		$emig_post->save();

		Session::flash('success','Employer detail updated!');
		return redirect()->back();

	}

	public function savePolicypdf(Request $request,$id){
		$emig_post = Emigration::find($id);

		if($request->hasFile('policy_pdf')){
			$file = $request->file('policy_pdf');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$policy_pdf = $name;
		}else{
			$policy_pdf = $emig_post->policy_pdf;
		}

		$emig_post->policy_pdf = $policy_pdf;
		$emig_post->save();

	}

	public function savefepdf(Request $request,$id){
		$emig_post = Emigration::find($id);

		if($request->hasFile('fe_pdf')){
			$file = $request->file('fe_pdf');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$fe_pdf = $name;
		}else{
			$fe_pdf = $emig_post->fe_pdf;
		}

		$emig_post->fe_pdf = $fe_pdf;
		$emig_post->save();

	}

	public function employeeUpdate(Request $request,$id){
		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		// Get File and Upload it

		if($request->hasFile('cand_sign')){
			$file = $request->file('cand_sign');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$cand_sign = $name;
		}else{
			$cand_sign = $emig_post->cand_sign;
		}

		if($request->hasFile('cand_photo')){
			$file = $request->file('cand_photo');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$cand_photo = $name;
		}else{
			$cand_photo = $emig_post->cand_photo;
		}

		if($request->hasFile('visa_delegation')){
			$file = $request->file('visa_delegation');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$visa_delegation = $name;
		}else{
			$visa_delegation = $emig_post->visa_delegation;
		}

		if($request->hasFile('pass_copy')){
			$file = $request->file('pass_copy');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			// $name = $filecount . '.' . $file_exe;
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$pass_copy = $name;
		}else{
			$pass_copy = $emig_post->pass_copy;
		}

		if($request->hasFile('visa_copy')){
			$file = $request->file('visa_copy');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			// $name = $filecount . '.' . $file_exe;
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$visa_copy = $name;
		}else{
			$visa_copy = $emig_post->visa_copy;
		}

		// Update Emigration Data

		$emig_post->cand_name = $request->cand_name;
		$emig_post->cand_hindi_name = $request->cand_hindi_name;
		$emig_post->salary = $request->salary;
		$emig_post->father_name = $request->father_name;
		$emig_post->spouse_name = $request->spouse_name;
		// $emig_post->cand_address = $request->cand_address;
		$emig_post->cand_address1 = $request->cand_address1;
		$emig_post->cand_address2 = $request->cand_address2;
		$emig_post->up_docs = $request->up_docs;
		$emig_post->cand_sign = $cand_sign;
		$emig_post->cand_photo = $cand_photo;
		$emig_post->visa_delegation = $visa_delegation;
		$emig_post->pass_copy = $pass_copy;
		$emig_post->visa_copy = $visa_copy;
		$emig_post->cand_pincode = $request->cand_pincode;

		$emig_post->save();

		Session::flash('success','Candidate details updated!');
		return redirect()->back();
	}

	public function emigupdate($id){
		// // Get Emigration Details
		// $emig_det = Emigration::find($id);
		// // Get Emigration Status
		// $emig_st = EmigrationStatus::where('emig_id','=',$id)->first();
		// // Get Candidate Details
		// $cand_det = Candidate::where('cand_id','=',$emig_det->cand_id)->first();
		// // Get Employer Details
		// $emp_det = Employee::where('emp_id','=',$emig_det->emp_id)->first();
		// // Party Details
		// $party_det = Party::where('pty_id','=',$emig_det->pty_id)->first();
		
		$emig_det = DB::table('emigrations as emigr')
			->join('emigration_statuses as emigst','emigst.cand_id','=','emigr.cand_id')
			->leftjoin('qr_candidate_tbl as cand','cand.cand_id','=','emigr.cand_id')
			->leftjoin('qr_employee_candidate as emp_cand','emp_cand.cand_id','=','emigr.cand_id')
			->leftjoin('qr_employee_tbl as emp','emp.emp_id','=','emp_cand.emp_id')
			->leftjoin('qr_party_tbl as pty','pty.pty_id','=','cand.pty_id')
			->leftjoin('qr_profession_tbl as prof','prof.prof_id','=','emp_cand.cand_profession')
			->leftjoin('qr_services_details as ser_det','ser_det.cand_id','emigr.cand_id')
			->leftjoin('users as user','user.user_id','=','emigr.confirmby_id')
			->leftjoin('qr_country as country','country.country_id','=','emp.country')
			->select('emigr.*','cand.cand_pre_address','user.name as uname','pty.pty_ag_name as agname','emp.wakala_type as waktype','cand.cand_photo as candPhoto','emp.spon_nm_arab','emp.spon_nm_eng','emp.emp_visa_no as evisa','emp.emp_id_no as eid','country.country_name','ser_det.mofa as emofa','prof.prof_eng_name')
			->where('emigr.id','=',$id)
			->first();

			$mofa_data = unserialize($emig_det->emofa);
	

			// Emigration Status
					
			// New Documents Status
			$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();
			if($emig_st->new_documents == 0 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.new_documents',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}

			// Document Received Status
			if($emig_st->emp_docs_rec == 0 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.waiting_for_document',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}


			// Waiting for candidate status
			if(
				($emig_st->waiting_for_cand_docs == 0 && $emig_st->ready_emig_reg == 0 && $emig_st->cand_emp_status == 0 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0) ||
				($emig_st->waiting_for_cand_docs == 0 && $emig_st->ready_emig_reg == 1 && $emig_st->cand_emp_status == 1 && $emig_st->cand_empd_status == 0 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0) ||
				($emig_st->waiting_for_cand_docs == 0 && $emig_st->ready_emig_reg == 1 && $emig_st->ready_for_dem_raised == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0)	
			){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.waiting_for_cand_docs',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}

			// Ready for Employer registration
			if(
				($emig_st->ready_emig_reg == 0 && $emig_st->cand_emp_status == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0) ||
				($emig_st->ready_emig_reg == 0 && $emig_st->waiting_for_cand_docs == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0)
			){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.ready_for_emp_reg',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}

			// Ready for demand raised
			if(
				($emig_st->ready_for_dem_raised == 0 && $emig_st->cand_empd_status == 1 && $emig_st->ready_emig_reg == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0) || 
				($emig_st->ready_for_dem_raised == 0 && $emig_st->ready_emig_reg == 1 && $emig_st->waiting_for_cand_docs == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0)
			){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.ready_for_demand_raised',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}

			// Ready for job assign
			if($emig_st->ready_for_job_assign == 0 && $emig_st->ready_for_dem_raised == 1 && $emig_st->ready_emig_reg == 1 && $emig_st->waiting_for_cand_docs == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.ready_for_job_assign',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}

			// if($emig_st->waiting_for_cand_docs == 0 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0){
			// 	$pageConfigs = ['pageHeader' => false];
			// 	return view('content.service.emigration.waiting_for_cand_docs',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data]);
			// }

			// Ready for emigration apply
			if($emig_st->ready_for_emig_apply == 0 && $emig_st->ready_for_job_assign == 1 && $emig_st->ready_for_dem_raised == 1 && $emig_st->ready_emig_reg == 1 && $emig_st->waiting_for_cand_docs == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.ready_for_emig_apply',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}

			// Emigration Applied
			if(
				($emig_st->applied == 0 && $emig_st->ready_for_emig_apply == 1 && $emig_st->ready_for_job_assign == 1 && $emig_st->ready_for_dem_raised == 1 && $emig_st->ready_emig_reg == 1 && $emig_st->waiting_for_cand_docs == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0) ||
				($emig_st->applied == 0 && $emig_st->already_exists == 1 && $emig_st->al_ready_exist_sub == 1)	
			){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.applied',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}

			// Emigration Approved

			if(
				($emig_st->approved == 0 && $emig_st->rejection == 1 && $emig_st->applied == 1 && $emig_st->ready_for_emig_apply == 1 && $emig_st->ready_for_job_assign == 1 && $emig_st->ready_for_dem_raised == 1 && $emig_st->ready_emig_reg == 1 && $emig_st->waiting_for_cand_docs == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0) || 
				($emig_st->approved == 0 && $emig_st->rejection == 1 && $emig_st->applied == 1 && $emig_st->already_exists == 1 && $emig_st->al_ready_exist_sub == 1)
			){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.approved',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}

			// Emigration Reject

			if(
				($emig_st->rejection == 0 && $emig_st->approved == 1 && $emig_st->applied == 1 && $emig_st->ready_for_emig_apply == 1 && $emig_st->ready_for_job_assign == 1 && $emig_st->ready_for_dem_raised == 1 && $emig_st->ready_emig_reg == 1 && $emig_st->waiting_for_cand_docs == 1 && $emig_st->emp_docs_rec == 1 && $emig_st->new_documents == 1 && $emig_st->already_exists == 0 && $emig_st->ass_cancel == 0 && $emig_st->hold == 0) || 
				($emig_st->rejection == 0 && $emig_st->approved == 1 && $emig_st->applied == 1 && $emig_st->already_exists == 1 && $emig_st->al_ready_exist_sub == 1)
			){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.rejection',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}


			if($emig_st->already_exists == 1 && $emig_st->al_ready_exist_sub == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.emigration.already_exist',['pageConfigs' => $pageConfigs,'emig_det' => $emig_det,'mofa_data' => $mofa_data,'emig_st' => $emig_st]);
			}



			if($emig_st->hold == 0){
				$pageConfigs = ['pageHeader' => false];
			}

			if($emig_st->ass_cancel == 0){
				$pageConfigs = ['pageHeader' => false];
			}
		
	}



	public function alreadyexiststr(Request $request,$id){
		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		// Get File and Upload it

		if($request->hasFile('policy')){
			$file = $request->file('policy');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$policy = $name;
		}else{
			$policy = $emig_post->policy;
		}

		// update in emigration
		$emig_post->text_msg = $request->text_msg;
		$emig_post->en_number = $request->en_number;
		$emig_post->policy = $policy;

		// update in emigration status
		if($request->already_exists == 1){
			$emig_st->al_ready_exist_sub = 1;
			$emig_st->status_name = 'Emigration applied';
		}

		$emig_post->save();
		$emig_st->save();
		
		Session::flash('success','Status updated!');
		return redirect()->back();

	}

	public function emgapplied(Request $request,$id){
		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		// change status based on request
		if($request->emig_applied == 0){

			// upload sticker
			if($request->hasFile('emig_sticker')){
				$file = $request->file('emig_sticker');
				// $file_count = File::files(base_path().'/public/image/emigration/employer');
				// $filecount = 0;
				// if ($file_count !== false) {
				// 	$filecount = count($file_count);
				// }
				// $file_exe = $file->getClientOriginalExtension();
				$name = time(). '_' . $file->getClientOriginalName();
				$file->move(base_path().'/public/image/emigration/employer',$name);
				$emig_sticker = $name;
			}else{
				$emig_sticker = $emig_post->emig_sticker;
			}

			// update in emigration details
			$emig_post->emig_sticker = $emig_sticker;

			// update in emigration status
			$emig_st->applied = 1;
			$emig_st->approved = 0;
			$emig_st->rejection = 1;
			$emig_st->status_name = "Approved";

			$emig_post->save();
			$emig_st->save();

			// Campaign List for Emigration

			$campList = new EmigrationCampaignList();
			$campList->cand_name = $emig_post->cand_name;
			$campList->pass_no = $emig_post->pass_no;
			$campList->subject = "Emigration approved of ".$emig_post->cand_name." having passport no ".$emig_post->pass_no;
			$campList->file = $emig_sticker;
			$campList->save();

			// Get Party Details
			$party = Party::where('pty_id','=',$emig_post->pty_id)->first();

			// Get Whatsapp API
			$getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();

			// Create Job For send sticker
			EmigrationApproved::dispatch($campList,$party,$getAPI,$emig_post)->onQueue('emigration');

		}else{
			// update in emigration details
			$emig_post->emig_sticker = $request->rejection_box;

			// update in emigration status befor 1-26-2023
			$emig_st->applied = 1;
			$emig_st->approved = 1;
			$emig_st->rejection = 0;
			$emig_st->status_name = "Reject";

			// update in emigration status after 1-26-2023

			$emig_st->applied = 0;
			$emig_st->ready_for_emig_apply = 0;
			$emig_st->status_name = "Reject";

			$emig_post->save();
			$emig_st->save();

		}


		
		

		Session::flash('success','Status updated!');
		return redirect()->back();
	}

	public function readyfemigapp(Request $request,$id){

		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();
		// Get File and Upload it
		if($request->hasFile('visa_copy')){
			$file = $request->file('visa_copy');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$visa_copy = $name;
		}else{
			$visa_copy = $emig_post->visa_copy;
		}

		if($request->hasFile('policy_pdf')){
			$file = $request->file('policy_pdf');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$policy_pdf = $name;
		}else{
			$policy_pdf = $emig_post->policy_pdf;
		}

		if($request->hasFile('fe_pdf')){
			$file = $request->file('fe_pdf');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$fe_pdf = $name;
		}else{
			$fe_pdf = $emig_post->fe_pdf;
		}

		// Update Emigration Data
		// $emig_post->can_pt_number = $request->can_pt_number;
		$emig_post->en_number = $request->en_number;
		$emig_post->policy_number = $request->policy_number;

		$emig_post->visa_copy = $visa_copy;
		$emig_post->policy_pdf = $policy_pdf;
		$emig_post->fe_pdf = $fe_pdf;
		
		$emig_post->emig_payment_st = $request->emig_payment_st;
		$emig_post->applied_date = $request->applied_date;

		$emig_post->appliedby_id = Auth::user()->user_id;

		


		if($request->emig_payment_st == 1){
			$emig_st->ready_for_emig_apply = 1;
			$emig_st->status_name = "Emigration applied";

			// get employer details
			$employer_det = Employee::where('emp_id','=',$emig_post->emp_id)->first();

			
			// Party details
			$party = Party::where('pty_id','=',$emig_post->pty_id)->first();
			// Whatsapp API Details
			$getApi = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();
			// Send Notification
			$emig_camp = new EmigrationCampaignList();
			$emig_camp->cand_name = $emig_post->cand_name;
			$emig_camp->pass_no = $emig_post->pass_no;
			$emig_camp->emig_app_date = date('Y-m-d');
			$emig_camp->en_no = $request->en_number;
			$emig_camp->sponsor_name = $emig_post->emp_name_eng;
			$emig_camp->sponsor_id = $employer_det->emp_id_no;
			$emig_camp->subject = "Emigration successful apply";
			$emig_camp->file = $policy_pdf;
			$emig_camp->save();

			// create job
			EmigrationSuccApp::dispatch($emig_camp,$party,$getApi,$emig_post)->onQueue('emigration');
			
		}

		$emig_post->save();
		$emig_st->save();

		Session::flash('success','Status updated!');
		return redirect()->back();
	}

	public function readyforjbass(Request $request,$id){
		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		// Update Emigration Data
		$emig_post->jb_number = $request->jb_number;
		$emig_post->can_pt_number = $request->can_pt_number;

		// Update in Emigration Status
		$emig_st->ready_for_job_assign = 1;
		$emig_st->status_name = "Ready for emigration apply";

		$emig_post->save();
		$emig_st->save();

		Session::flash('success','Status updated!');
		return redirect()->back();
	}

	public function readfordemraisedstr(Request $request,$id){
		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		// Update Emigration Data
		$emig_post->pt_number = $request->pt_number;
		$emig_post->demand_number = $request->demand_number;
		$emig_post->no_vacancy = $request->no_vacancy;

		// Update in Emigration Status
		$emig_st->ready_for_dem_raised = 1;
		if($emig_st->waiting_for_cand_docs == 1){
			$emig_st->status_name = "Ready for job assign";
		}else{
			$emig_st->status_name = "Waiting for candidate document";
		}

		$emig_post->save();
		$emig_st->save();
		Session::flash('success','Status updated!');
		return redirect()->back();

	}

	public function readempregstr(Request $request,$id){
		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		// Update Emigration Data
		$emig_post->pt_number = $request->pt_number;
		$emig_post->fe_number = $request->fe_number;

		// Update in Emigration Status
		$emig_st->ready_emig_reg = 1;
		$emig_st->status_name = "Ready For Demand Raised";
		
		$emig_post->save();
		$emig_st->save();

		Session::flash('success','Status updated!');
		return redirect()->back();

	}

	public function canddocstr(Request $request,$id){
		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		// Get File and Upload it

		if($request->hasFile('cand_sign')){
			$file = $request->file('cand_sign');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$cand_sign = $name;
		}else{
			$cand_sign = $emig_post->cand_sign;
		}

		if($request->hasFile('cand_photo')){
			$file = $request->file('cand_photo');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$cand_photo = $name;
		}else{
			$cand_photo = $emig_post->cand_photo;
		}

		if($request->hasFile('visa_delegation')){
			$file = $request->file('visa_delegation');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$visa_delegation = $name;
		}else{
			$visa_delegation = $emig_post->visa_delegation;
		}

		if($request->hasFile('pass_copy')){
			$file = $request->file('pass_copy');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			// $name = $filecount . '.' . $file_exe;
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$pass_copy = $name;
		}else{
			$pass_copy = $emig_post->pass_copy;
		}

		if($request->hasFile('visa_copy')){
			$file = $request->file('visa_copy');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			// $name = $filecount . '.' . $file_exe;
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$visa_copy = $name;
		}else{
			$visa_copy = $emig_post->visa_copy;
		}

		// Update Emigration Data

		$emig_post->cand_hindi_name = $request->cand_hindi_name;
		$emig_post->salary = $request->salary;
		$emig_post->father_name = $request->father_name;
		$emig_post->spouse_name = $request->spouse_name;
		// $emig_post->cand_address = $request->cand_address;
		$emig_post->cand_address1 = $request->cand_address1;
		$emig_post->cand_address2 = $request->cand_address2;
		$emig_post->up_docs = $request->up_docs;
		$emig_post->cand_sign = $cand_sign;
		$emig_post->cand_photo = $cand_photo;
		$emig_post->visa_delegation = $visa_delegation;
		$emig_post->pass_copy = $pass_copy;
		$emig_post->visa_copy = $visa_copy;
		$emig_post->cand_pincode = $request->cand_pincode;
		$emig_post->emplcreateby_id = Auth::user()->user_id;

		if($request->up_docs == 1){
			// Update Emigration Status
			$emig_st->waiting_for_cand_docs = 1;
			$emig_st->cand_emp_status = 1;
			$emig_st->cand_empd_status = 1;
			$emig_st->status_name = "Ready For Employer Registration";
		}else{
			if($emig_st->ready_emig_reg == 0 && $emig_st->ready_for_dem_raised == 0){
				$emig_st->cand_emp_status = 1;
				$emig_st->status_name = "Ready For Employer Registration";
			}
			if($emig_st->ready_emig_reg == 1 && $emig_st->ready_for_dem_raised == 0){
				$emig_st->cand_empd_status = 1;
				$emig_st->status_name = "Ready For Demand Raised";
			}
		}

		$emig_post->save();
		$emig_st->save();

		Session::flash('success','Status updated!');
		return redirect()->back();

	}

	public function empdocstr(Request $request,$id){
		// dd($request);
		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		// Get File and Upload it

		if($request->hasFile('national_id')){
			$file = $request->file('national_id');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$national_id = $name;
		}else{
			$national_id = $emig_post->national_id;
		}

		if($request->hasFile('sign')){
			$file = $request->file('sign');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$sign = $name;
		}else{
			$sign = $emig_post->sign;
		}

		if($request->hasFile('crcopy')){
			$file = $request->file('crcopy');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$crcopy = $name;
		}else{
			$crcopy = $emig_post->crcopy;
		}

		if($request->hasFile('stamp')){
			$file = $request->file('stamp');
			// $file_count = File::files(base_path().'/public/image/emigration/employer');
			// $filecount = 0;
			// if ($file_count !== false) {
			// 	$filecount = count($file_count);
			// }
			// $file_exe = $file->getClientOriginalExtension();
			// $name = $filecount . '.' . $file_exe;
			$name = time(). '_' . $file->getClientOriginalName();
			$file->move(base_path().'/public/image/emigration/employer',$name);
			$stamp = $name;
		}else{
			$stamp = $emig_post->stamp;
		}


		// Update in Emigration
		$emig_post->emp_name_eng = $request->emp_name_eng;
		$emig_post->emp_address = $request->emp_address;
		$emig_post->po_box_no = $request->po_box_no;
		$emig_post->contact_number = $request->contact_number;
		$emig_post->email = $request->email;
		$emig_post->emp_city = $request->emp_city;
		$emig_post->emp_pincode = $request->emp_pincode;
		$emig_post->emp_phone = $request->emp_phone;
		$emig_post->national_id = $national_id;
		$emig_post->sign = $sign;
		$emig_post->crcopy = $crcopy;
		$emig_post->stamp = $stamp;
		$emig_post->empdocup = $request->emp_docu;
		// $emig_post->wakala_type = $request->wakala_type;
		
		$emig_post->company_name = $request->company_name;
		$emig_post->comp_reg_no = $request->comp_reg_no;
		$emig_post->pid = $request->pid;
		$emig_post->empcreatedby_id = Auth::user()->user_id;
		
		$emig_post->save();

		// Update Emigration Status
		if($request->emp_docu == 1){
			$emig_st->emp_docs_rec = 1;
			$emig_st->status_name = 'Ready For Employer Registration';
			$emig_st->save();
		}

		Session::flash('success','Status updated!');
		return redirect()->back();
	}

	public function emgnewdocs(Request $request,$id){

		// dd($request);

		$emig_post = Emigration::find($id);
		$emig_st = EmigrationStatus::where('emig_id','=',$id)->first();

		$emig_post->erse = $request->already_exists;
	
		if($request->already_exists == 1){
			// update in Emigration
			$emig_post->text_msg = $request->text_msg;
			$emig_post->confirmby_id = Auth::user()->user_id;
			// update in Emigration Status
			$emig_st->new_documents = 1;
			$emig_st->already_exists = 1;
			$emig_st->status_name = 'Already Exist';

			// get employer details
			$employer_det = Employee::where('emp_id','=',$emig_post->emp_id)->first();
			// Party details
			$party = Party::where('pty_id','=',$emig_post->pty_id)->first();
			// Whatsapp API Details
			$getApi = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();
			// Send Notification
			$emig_camp = new EmigrationCampaignList();
			$emig_camp->cand_name = $emig_post->cand_name;
			$emig_camp->pass_no = $emig_post->pass_no;
			$emig_camp->sponsor_name = $emig_post->emp_name_eng;
			$emig_camp->sponsor_id = $employer_det->emp_id_no;
			$emig_camp->subject = "Sponsor already exit on Emigtaion site";
			$emig_camp->save();
			// create job
			EmigrationSpnAlx::dispatch($emig_camp,$party,$getApi,$emig_post)->onQueue('emigration');
		}else{
			
			// Update in Emigration Status
			if($request->emp_docs_st == 1){
				$emig_st->new_documents = 1;
				$emig_st->emp_docs_rec = 0;
				$emig_st->waiting_for_emp_docs = 0;
				$emig_st->status_name = 'Employer Docs Received';
			}else{
				$emig_st->new_documents = 0;
				$emig_st->emp_docs_rec = 0;
				$emig_st->waiting_for_emp_docs = 1;
				$emig_st->status_name = 'Waiting For Employer Docs';

				// get employer details
				$employer_det = Employee::where('emp_id','=',$emig_post->emp_id)->first();
				// Party details
				$party = Party::where('pty_id','=',$emig_post->pty_id)->first();
				// Whatsapp API Details
				$getApi = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();
				// Send Notification
				$emig_camp = new EmigrationCampaignList();
				$emig_camp->cand_name = $emig_post->cand_name;
				$emig_camp->pass_no = $emig_post->pass_no;
				$emig_camp->sponsor_name = $emig_post->emp_name_eng;
				$emig_camp->sponsor_id = $employer_det->emp_id_no;
				$emig_camp->subject = "Documents Required";
				$emig_camp->save();
				// create job
				EmigrationDocReq::dispatch($emig_camp,$party,$getApi,$emig_post)->onQueue('emigration');

			}
		}

		$emig_post->save();
		$emig_st->save();

		Session::flash('success','Status Updated!');
		return redirect()->back();
	}

	public function emigration_list_json()
	{
		$data_cand = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		->join('qr_services_status as ser_status', 'cand.cand_id', '=', 'ser_status.cand_id')
		->leftjoin('qr_employee_tbl as emp', 'emp_cand.emp_id', '=', 'emp.emp_id')
		->leftjoin('qr_party_tbl as pty', 'cand.pty_id', '=', 'pty.pty_id')
		->leftjoin('qr_profession_tbl as pf', 'cand.prof_id', '=', 'pf.prof_id')
		->leftjoin('qr_candidate_service as cand_ser', 'cand_ser.cand_id', '=', 'cand.cand_id')
		->leftjoin('qr_services_payment_status as pst', 'cand.cand_id', '=', 'pst.cand_id')
		->select('cand.cand_id','cand.cand_passport_no','emp.emp_file_no','cand.created_at','pty.pty_ag_name',
			'cand.cand_fname','emp.emp_id_no','emp.emp_visa_no','pf.prof_eng_name','ser_status.visa_reject','ser_status.mofa_paid') 
		->where('cand_ser.cand_passport_type','=','ECR')
		->orderBy('cand.cand_id','DESC');
		
		return DataTables::of($data_cand)->toJson();
	}

	public function emigration_view($id)
	{	

		// $cand_data = DB::table('qr_candidate_tbl as cand')
		// ->leftjoin('qr_services_details as ser', 'cand.cand_id', '=', 'ser.cand_id')
		// ->leftjoin('qr_candidate_service as cand_ser', 'cand.cand_id', '=', 'cand_ser.cand_id')
		// ->leftjoin('qr_emp_visa_tbl as vs', 'vs.visa_id', '=', 'cand_ser.cand_visa_type')
		// ->leftjoin('qr_party_tbl as pty', 'pty.pty_id', '=', 'cand.pty_id')
		// ->leftjoin('qr_nationality_tbl as nt', 'nt.nat_id', '=', 'cand.nat_id')
		// ->leftjoin('qr_religion_cast_tbl as cst', 'cst.rel_cst_id', '=', 'cand.rel_cst_id')
		// ->leftjoin('qr_profession_tbl as prof', 'prof.prof_id', '=', 'cand.prof_id')

		// ->select('cand.*','ser.mofa as mofa','ser.medicle as medicle','ser.medicle_online_update as medup_data','cand.created_at as cand_created','cand_ser.cand_passport_type','cand_ser.cand_visa_type','cand_ser.cand_service_type','vs.visa_name','pty.*','nt.*','cst.*','prof.*')
		// ->where('cand.cand_id', '=', $id)
		
		// ->first();

		$emig_det = DB::table('emigrations as emig')
			->leftjoin('emigration_statuses as emig_st','emig_st.emig_id','=','emig.id')
			->leftjoin('qr_candidate_tbl as cand','cand.cand_id','=','emig.cand_id')
			->leftjoin('qr_employee_candidate as emp_cand','emp_cand.cand_id','=','emig.cand_id')
			->leftjoin('qr_employee_tbl as emp','emp.emp_id','emp_cand.emp_id')
			->leftjoin('qr_party_tbl as party','party.pty_id','=','cand.pty_id')
			->leftjoin('qr_profession_tbl as proff','proff.prof_id','=','emp_cand.cand_profession')
			->select('emig.*','party.pty_ag_name','proff.prof_eng_name','cand.cand_photo as CandPhoto')
			->where('emig.id','=',$id)
			->first();


		$pageConfigs = ['pageHeader' => false];
		return view('/content/service/emigration-view', ['cand_data' => $emig_det,'pageConfigs' => $pageConfigs]);
	}

	function emigration_store(Request $request)
	{

			if ($request->hasFile('cand_photo')) {
			
			$file = $request->file('cand_photo');
			foreach ($file as  $file) {
				
				$file_count = File::files(base_path().'/public/image/service-candidate');
				$filecount = 0;

				if ($file_count !== false) {
					$filecount = count($file_count);
				}
				$file_exe = $file->getClientOriginalExtension();
				$name = $filecount . '.' . $file_exe;

				$file->move(base_path().'/public/image/service-candidate', $name);
				$candidate_photo[] = $name;
			}

		} else {
			$candidate_photo[] = '';
		}

		if ($request->hasFile('cand_passport_doc')) {
			$file = $request->file('cand_passport_doc');
			foreach ($file as  $file) {
				$file_count = File::files(base_path().'/public/image/service-candidate');
				$filecount = 0;

				if ($file_count !== false) {
					$filecount = count($file_count);
				}
				$file_exe = $file->getClientOriginalExtension();
				$name = $filecount . '.' . $file_exe;

				$file->move(base_path().'/public/image/service-candidate', $name);
				$candidate_passport[] = $name;
			}
		} else {
			$candidate_passport[] = '';
		}
		/**/


		if ($request->hasFile('cand_cv')) {
			$file = $request->file('cand_cv');
			foreach ($file as  $file) {
				$file_count = File::files(base_path().'/public/image/service-candidate');
				$filecount = 0;

				if ($file_count !== false) {
					$filecount = count($file_count);
				}
				$file_exe = $file->getClientOriginalExtension();
				$name = $filecount . '.' . $file_exe;

				$file->move(base_path().'/public/image/service-candidate', $name);
				$candidate_cv[] = $name;
			}
		} else {
			$candidate_cv[] = '';
		}
		/**/

		if ($request->hasFile('cand_doc')) {
			$file = $request->file('cand_doc');
			foreach ($file as  $file) {
				$file_count = File::files(base_path().'/public/image/service-candidate');
				$filecount = 0;

				if ($file_count !== false) {
					$filecount = count($file_count);
				}
				$file_exe = $file->getClientOriginalExtension();
				$name = $filecount . '.' . $file_exe;

				$file->move(base_path().'/public/image/service-candidate', $name);
				$candidate_doc[] = $name;
			}
		} else {
			$candidate_doc[] = '';
		}
        //
        // }
        // dd( $request );
		$candidate = new Candidate();
		$candidate->pty_id = $request->input('pty_id');

		$candidate->cand_pass_status = $request->input('cand_pass_status');
		$candidate->pass_date = $request->input('pass_date');
		$candidate->pass_locatpassn = $request->input('pass_locatpassn');
		$candidate->cand_passport_type = $request->input('cand_passport_type');
		$candidate->user_id = Auth::user()->user_id;
		$candidate->user_type = Auth::user()->user_type;
		$candidate->cand_fname = $request->input('cand_fname');
		$candidate->cand_mname = $request->input('cand_mname');
		$candidate->cand_lname = $request->input('cand_lname');
		$candidate->cand_passport_no = $request->input('cand_passport_no');
		$candidate->cand_place_birth = $request->input('cand_place_birth');
		$candidate->cand_place_issue = $request->input('cand_place_issue');
		$candidate->cand_date_issue = $request->input('cand_date_issue');
		$candidate->cand_date_expiry = $request->input('cand_date_expiry');
		$candidate->nat_id = $request->input('nat_id');
		$candidate->cand_visa_type = $request->input('cand_visa_type');
		$candidate->cand_dob = $request->input('cand_dob');
		$candidate->cand_gender = $request->input('cand_gender');
		$candidate->rel_cst_id = $request->input('rel_cst_id');
		$candidate->prof_id = $request->input('prof_id');
		$candidate->cand_mobile_no = $request->input('cand_mobile_no');
		$candidate->travel_from = $request->input('travel_from') ? $request->input('travel_from') :'';
		$candidate->travel_to = $request->input('travel_to') ? $request->input('travel_to') :'';
		$candidate->travel_start_date = $request->input('travel_start_date') ? $request->input('travel_start_date') : '';
		$candidate->travel_end_date = $request->input('travel_end_date') ? $request->input('travel_end_date') :'';

		$candidate->cand_pre_address = $request->input('cand_pre_address');
		$candidate->cand_local_note = $request->input('cand_local_note');
		$candidate->cand_photo = implode(',', $candidate_photo);
		$candidate->cand_passport_doc = implode(',', $candidate_passport);
		$candidate->cand_cv = implode(',', $candidate_cv);
		$candidate->cand_doc = implode(',', $candidate_doc);
        // dd(implode( ',', $candidate_photo ));
		$candidate->save();

		$cand_last_id = $candidate->cand_id;
   

		$cnt_cand_ser = CandidateService::count();
        
		$cnt_id = ($cnt_cand_ser + 1);
		$cs_ser_id = 'QSI/' . date('ym') . '/' . $cnt_id;
		$cs_inv_id = 'QCINV/' . date('ym') . '/' . $cnt_id;
        // dd( $cs_ser_id );
		$candidate_ser = new CandidateService;
		$candidate_ser->cand_id = $cand_last_id;
		$candidate_ser->cand_visa_type = $request->input('cand_visa_type');
		$candidate_ser->pty_id = $request->input('pty_id');
		$candidate_ser->cs_ser_id = $cs_ser_id;
		$candidate_ser->cand_passport_type = 'ECR';

		$candidate_ser->cand_service_type =  '2';
		$candidate_ser->cand_service_status = 'A';
		$candidate_ser->invoice_id = $cs_inv_id;
		$candidate_ser->save();

		if ($request->input('cand_pass_status') == '2') {

			$service_status = new ServiceStatus;
			$service_status->cand_id = $cand_last_id;
			$service_status->cs_ser_id = $request->input('cs_ser_id');
			$service_status->user_id = Auth::id();
			$service_status->cand_passport_no = $request->input('cand_passport_no');
			$service_status->passport = 1;
			$service_status->save();

			$service_details = new ServiceDetails;
			$service_details->cand_id = $cand_last_id;
			$service_details->cs_ser_id = $request->input('cs_ser_id');
			$service_details->user_id = Auth::id();
			$service_details->cand_passport_no = $request->input('cand_passport_no');
			$service_details->passport = '';
			$service_details->save();

			$service_pay_status = new ServicePaymentStatus;
			$service_pay_status->cand_id = $cand_last_id;

			$service_pay_status->user_id = Auth::id();
			$service_pay_status->cand_passport_no = $request->input('cand_passport_no');
			$service_pay_status->save();

		}
		if ($request->input('cand_pass_status') == '1') {

			$service_status = new ServiceStatus;
			$service_status->cand_id = $cand_last_id;
			$service_status->cs_ser_id = $request->input('cs_ser_id');
			$service_status->user_id = Auth::id();
			$service_status->cand_passport_no = $request->input('cand_passport_no');

			$service_status->save();

			$service_details = new ServiceDetails;
			$service_details->cand_id = $cand_last_id;
			$service_details->cs_ser_id = $request->input('cs_ser_id');
			$service_details->user_id = Auth::id();
			$service_details->cand_passport_no = $request->input('cand_passport_no');

			$service_details->save();

			$service_pay_status = new ServicePaymentStatus;
			$service_pay_status->cand_id = $cand_last_id;

			$service_pay_status->user_id = Auth::id();
			$service_pay_status->cand_passport_no = $request->input('cand_passport_no');
			$service_pay_status->save();


		}
		Session::flash('success', 'Candidate created successfully !');
		return redirect()->back();

	}

	public function check_visa_date(Request $request)
	{
		$exp_date = $request->input('exp_date') ? $request->input('exp_date') : '';
		$today_date = date('Y-m-d');

		$exp_date = Carbon::parse($exp_date);
		$today_date = Carbon::parse($today_date);

		$date_diff = $today_date->diffInMonths($exp_date);
		$date_comp = $exp_date->gt($today_date);


		if($date_comp)  
		{

			if($date_diff < 9)
			{
				return response()->json('0');
			}
			else{
				return response()->json('1');
			}

		}else{
			return response()->json('2');
		}
	}

	public function mofa_explode(Request $request)
	{
		$cand_id = $request->input('cand_id');
		$data = Servicedetails::where('cand_id','=',$cand_id)->first();
		$mofa_data = unserialize($data->mofa);
	
		return response()->json($mofa_data);
	}

	public function mofa_status_update(Request $request)
	{
		global $status;
		$status = $request->input('status') ? $request->input('status') : '';
		$name = $request->input('name') ? $request->input('name') : '';
		$name = str_replace(' ', '_', $name);
		$cand_id = $request->input('cand_id') ? $request->input('cand_id') : '';

		$st_data = ServiceStatus::where('cand_id','=',$cand_id)->first();
		$ser_data = ServiceDetails::where('cand_id','=',$cand_id)->first();	

		$pass_data = unserialize($ser_data->mofa);
		$pass_data[$name] = $status;
		
		
		$pass_data_ser = serialize($pass_data);
		$ser_data->mofa = $pass_data_ser;
		$ser_data->update();
	

		$cat_data = DB::table('qr_candidate_tbl as cand')
		->leftjoin('qr_mofa_price as mfp','mfp.ser_id','=','cand.cand_visa_type')
		->leftjoin('qr_mofa_payment_category as mfpc','mfpc.id','=','mfp.pay_cat')
		->select('mfpc.*')
		->where('cand.cand_id','=',$cand_id)
		->get();

		// foreach ($cat_data as  $data) {
		// 	$data_name = str_replace(' ', '_', $data->name);
		// 	if(array_key_exists($data_name,$pass_data) ){
		// 		if($pass_data[$data_name]!=1 || $pass_data[$data_name]=='') {
		// 	 		$st_data->mofa_unpaid = 1;
		// 	 		$st_data->passport_original = 0;
		// 	 		$st_data->mofa_paid = 0;
		// 			$st_data->update(); 

		// 			date_default_timezone_set('Asia/Kolkata');
		// 			$timeline = new Timeline();
		// 			$timeline->cand_id = $cand_id;
		// 			$timeline->subject =  'Mofa Unpaid';
		// 			$timeline->time = date("h:i:sa");
		// 			$timeline->status = 4;
		// 			$timeline->user_id = Auth::user()->user_id;
		// 			$timeline->save();

		//     	}else{ 
		// 			$st_data->mofa_paid = 1;
		// 			$st_data->mofa_unpaid = 0;
		// 			$st_data->passport_original = 1;
		// 			$st_data->update();

		//     		date_default_timezone_set('Asia/Kolkata');
		// 			$timeline = new Timeline();
		// 			$timeline->cand_id = $cand_id;
		// 			$timeline->subject =  'Mofa Paid';
		// 			$timeline->time = date("h:i:sa");
		// 			$timeline->status = 1;
		// 			$timeline->user_id = Auth::user()->user_id;
		// 			$timeline->save();
		// 	 	}			
		// 	} else{
		// 		$st_data->mofa_unpaid = 1;
		// 		$st_data->mofa_paid = 0;
		// 		$st_data->passport_original = 0;
		// 		$st_data->update();
		// 		break;
		// 	}
		// }
	


	

	}

	public function mofa_religion_check(Request $request){
		$religion = $request->input('religion') ? $request->input('religion') : '';
		$candID = $request->input('candID') ? $request->input('candID') : '';

		// get Candidate data
		$candidate = Candidate::where('cand_id','=',$candID)->first();

		if($candidate->rel_cst_id != $religion){
			return response()->json('1');
		}else{
			
		}
	}


	public function de_asign(Request $request){
		$pass_no = $request->input('passport');
		$cand_id = $request->input('cand_id');
		$emp_id = $request->input('emp_id');

		// dd($request);

		

		// Find and Delete Candidate Employee Table
		$e_data = EmployeeCandidate::where('cand_id','=',$cand_id)->first();
		// dd($e_data);
		$e_data->delete();

		// $ser_status = ServiceStatus::where('cand_passport_no', '=', $pass_no)->first();
		$ser_status = ServiceStatus::where('cand_id', '=', $cand_id)->first();
		if($ser_status->ass_candidate_to_employer == '1' && $ser_status->ready_for_documentation == '0' && $ser_status->submission == '0' && $ser_status->submitted == '0'){
			$ser_status->ass_candidate_to_employer = 0;
			$ser_status->save();
		}

		if($ser_status->ass_candidate_to_employer == '1' && $ser_status->ready_for_documentation == '1' && $ser_status->submission == '0' && $ser_status->submitted == '0'){
			$ser_status->ass_candidate_to_employer = 0;
			$ser_status->ready_for_documentation = 0;
			$ser_status->save();
		}

		if($ser_status->ass_candidate_to_employer == '1' && $ser_status->ready_for_documentation == '1' && $ser_status->submission == '1' && $ser_status->submitted == '0'){
			$ser_status->ass_candidate_to_employer = 0;
			$ser_status->ready_for_documentation = 0;
			$ser_status->submission = 0;
			$ser_status->save();
		}
		if($ser_status->ass_candidate_to_employer == '1' && $ser_status->ready_for_documentation == '1' && $ser_status->submission == '1' && $ser_status->submitted == '1'){
			$ser_status->ass_candidate_to_employer = 0;
			$ser_status->ready_for_documentation = 0;
			$ser_status->submission = 0;
			$ser_status->submitted = 0;

			// delete data from Service Details
			// $ser_det = ServiceDetails::where('cand_passport_no','=',$pass_no)->first();
			$ser_det = ServiceDetails::where('cand_id','=',$cand_id)->first();
			$ser_det->submitted = '';
			$ser_det->save();

			$ser_status->save();
		}

		// Update Candidate STatus
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		$cand_status->active_status = 'Waiting For Visa Number';
		$cand_status->active_sub_status = '';
		$cand_status->save();

		date_default_timezone_set('Asia/Kolkata');
		$timeline = new Timeline();
		$timeline->cand_id = $cand_id;
		$timeline->subject =  'Candidate De-Asign';
		$timeline->time = date("h:i:sa");
		$timeline->status = 1;
		$timeline->user_id = Auth::user()->user_id;
		$timeline->save();

		// Check and Remove Emigration Candidate
		$emig_det = Emigration::where('cand_id','=',$cand_id)->first();
		$emig_st = EmigrationStatus::where('cand_id','=',$cand_id)->first();

		$emig_det->delete();
		$emig_st->delete();

		Session::flash('success', 'Candidate De-Asign !');
		return redirect('services/candidate-status/' . $cand_id);
	}

	// Get Candidate Update Status Details
	public function getDetailsC(Request $request){

		global $done;

		$id = $request->input('id');

		$pass_data = Candidate::where('cand_id', '=', $id)->first();
		$pass_no = $pass_data->cand_passport_no;
		$cand_ser = CandidateService::where('cand_id', $id)->first();
		if($cand_ser){
			$cand_cat = explode(",", $cand_ser->cand_service_category);
		}

		$party_data = Party::where('pty_id', $pass_data->pty_id)->first();
		
		$mofa_d = ServiceDetails::where('cand_id', $id)->first();
		if($mofa_d){
			$mofa_data = unserialize($mofa_d->mofa);
		}	

		if(isset($pass_data) && isset($cand_ser) && isset($mofa_data)){
			$st_data = ServiceStatus::where('cand_id', $id)->first();
			return response()->json($st_data);			
		}else{
			return response()->json('none');
		}
		
		
	}

	public function exportCsv(Request $request){
		
		$cands = Candidate::all();

		// $cands = DB::table('qr_candidate_tbl as cand')
		// 		->leftjoin('qr_employee_candidate as emp_cand', 'cand.cand_id', '=', 'emp_cand.cand_id')
		// 		-> 


		$filename = "ServiceCandidateData.csv";

		$headers = array(
			"Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"	
		);

		$columns = array('CandidateID','UserID','FirstName');
		
		$callback = function() use($cands,$columns){
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			foreach($cands as $cand){
				$row['CandidateID']  = $cand->cand_id;
				$row['UserID']  = $cand->user_id;
				$row['FirstName']  = $cand->cand_fname;
				fputcsv($file, array($row['CandidateID'], $row['UserID'], $row['FirstName']));
			}

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
	}

	public function visa_cancel(Request $request){
		
		// dd($request);
		
		$cand_id = $request->input('cand_id');
		$ser_status = ServiceStatus::where('cand_id','=',$cand_id)->first();
		$ser_details = Servicedetails::where('cand_id','=',$cand_id)->first();
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		$cand_compl = ComplCandStatus::where('cand_id','=',$cand_id)->first();
		$cs_status = array($cand_compl->c_status,"Visa cancel");
		$visa_cancel = $request->input('visa_cancel');
		
		// Main Status
		$finalStatus = new CandCompFinalStatus();

		// Update in Stage and Status
		// $stage_f = CandStageF::where('cand_id','=',$cand_id)->first();
		// $status_f = CandCstatusF::where('cand_id','=',$cand_id)->first();
		// $incom_f = CandIncomF::where('cand_id','=',$cand_id)->first();

		if($visa_cancel == 1){
			// update in Servie Details
			$ser_details->req_by = $request->input('req_by');
			$ser_details->sr_no = $request->input('sr_no');
			$ser_details->notes = $request->input('notes');
			$ser_details->visa_cancel = $visa_cancel;
			$ser_details->save();


			// $stage_f->new_document = 0;
			// $stage_f->waiting_for_musaned_reg = 0;
			// $stage_f->waiting_for_dele_from_another_office = 0;
			// $stage_f->waiting_for_mofa = 0;
			// $stage_f->mofa_unpaid = 0;
			// $stage_f->waiting_for_med_fit = 0;
			// $stage_f->waiting_for_pass = 0;
			// $stage_f->waiting_for_visa = 0;
			// $stage_f->hold = 0;
			// $stage_f->required_docs = 0;
			// $stage_f->ready_for_docu = 0;
			// $stage_f->pass_ready_for_submi = 0;
			// $stage_f->submitted_cons = 0;
			// $stage_f->visa_stamp_rejection = 0;
			// $stage_f->wait_for_emi_apll = 0;
			// $stage_f->completed = 0;
			// $stage_f->ready_for_visa_can = 1;
			// $stage_f->sub_in_cons_for_visa_can = 0;
			// $stage_f->visa_canceled = 0;
			// $stage_f->user_id = Auth::user()->user_id;
			// $stage_f->save();

			// $status_f->visa_canceled = 0;
			// $status_f->process_completed = 0;
			// $status_f->user_id = Auth::user()->user_id;
			// $status_f->save();



			// Update in Service Status
			$ser_status->visa_cancel = 1;
			$ser_status->save();
			// Update in Candidate Status
			$cand_status->active_status = 'Ready for visa cancelation';
			$cand_status->active_sub_status = "Visa cancel";
			$cand_status->incomplete_status = '';
			$cand_status->save();
			// Complete Status
			$cand_compl->c_status = implode(",",$cs_status);
			$cand_compl->save();
			// create Timeline
			date_default_timezone_set('Asia/Kolkata');
			$timeline = new Timeline();
			$timeline->cand_id = $cand_id;
			$timeline->subject = 'Ready For Visa Cancel';
			$timeline->time = date("h:i:sa");
			$timeline->user_id = Auth::user()->user_id;
			$timeline->status = 1;
			$timeline->save();




			$chk_vc = CandCompFinalStatus::where('final_status','=','Visa cancel')->where('cand_id','=',$cand_id)->first();
			if($chk_vc == ''){
				$finalStatus->cand_id = $cand_id;
				$finalStatus->final_status = "Visa cancel";
				$finalStatus->save();
			}


		}else{
			// Update in Service Details
				$ser_details->visa_cancel = $visa_cancel;
				$ser_details->save();
		}

		
		Session::flash('success','Visa Cancel');
		return redirect('services/candidate-status/' . $cand_id);
	}


	public function visa_cancel_final(Request $request){
		// dd($request);
		$cand_id = $request->input('cand_id');
		$pass_no = $request->input('passport');
		$visa_cancel_final = $request->input('visa_cancel_final');

		$ser_status = ServiceStatus::where('cand_id','=',$cand_id)->first();
		$ser_details = Servicedetails::where('cand_id','=',$cand_id)->first();
		$cand_status = CandidateStatus::where('cand_id','=',$cand_id)->first();
		$cand_compl = ComplCandStatus::where('cand_id','=',$cand_id)->first();
		
		$cs_status = array($cand_compl->c_status,"Visa Stamped");

		// Main Status
		$finalStatus = new CandCompFinalStatus();

		// Update in Stage and Status
		// $stage_f = CandStageF::where('cand_id','=',$cand_id)->first();
		// $status_f = CandCstatusF::where('cand_id','=',$cand_id)->first();
		// $incom_f = CandIncomF::where('cand_id','=',$cand_id)->first();

		// Visa Cancel Final Not Blank
		if($visa_cancel_final != ''){
			// Visa Not cancel
			if($visa_cancel_final == 0){
				// Update in Service Status
				$ser_status->visa_cancel = 0;
				$ser_status->save();
				// Update in Service Details
				$ser_details->req_by = '';
				$ser_details->sr_no = '';
				$ser_details->notes = '';
				$ser_details->visa_cancel = '';
				$ser_details->save();
				// Update Candidate Status
				$cand_status->active_status = 'Completed';
				$cand_status->active_sub_status = "";
				$cand_status->incomplete_status = '';
				$cand_status->save();

				$cand_compl->c_status = implode(",",$cs_status);
				$cand_compl->Visa_Stamped = 1;
				$cand_compl->PROCCESS_COMPLETED = 1;
				$cand_compl->save();
				// Remove Complete Status

				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 0;
				// $stage_f->waiting_for_med_fit = 0;
				// $stage_f->waiting_for_pass = 0;
				// $stage_f->waiting_for_visa = 0;
				// $stage_f->hold = 0;
				// $stage_f->required_docs = 0;
				// $stage_f->ready_for_docu = 0;
				// $stage_f->pass_ready_for_submi = 0;
				// $stage_f->submitted_cons = 0;
				// $stage_f->visa_stamp_rejection = 0;
				// $stage_f->wait_for_emi_apll = 0;
				// $stage_f->completed = 1;
				// $stage_f->ready_for_visa_can = 0;
				// $stage_f->sub_in_cons_for_visa_can = 0;
				// $stage_f->visa_canceled = 0;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// $status_f->process_completed = 1;
				// $status_f->visa_canceled = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();


				// check and remove visa cancel status
				$status = explode(",",$cand_compl->c_status);
				$rem_status = ["Visa cancel"];
				$new_array = array_diff($status,$rem_status);
				$cand_compl->c_status = implode(",",$new_array);
				$cand_compl->save();

				// Check Visa Cancel
				$chk_vcf = CandCompFinalStatus::where('final_status','=','Visa cancel')->where('cand_id','=',$cand_id)->first();
				if(isset($chk_vcf)){
					$chk_vcf->delete();
				}

				$finalStatus->cand_id = $cand_id;
				$finalStatus->final_status = "Visa Stamped";
				$finalStatus->save();

				// created timeline
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Visa Not Cancel';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 1;
				$timeline->save();
			}
			// Submited in Consulated
			if($visa_cancel_final == 1){
				// Update in Service Detials
				$ser_details->visa_canceled_final = $visa_cancel_final;
				$ser_details->save();
				// Update in Service Status

				// Update in Candidate Status
				$cand_status->active_status = 'Submitted in consulate for visa cancelation';
				$cand_status->active_sub_status = "Visa cancel";
				$cand_status->incomplete_status = '';
				$cand_status->save();
				// Create Time Line
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Submitted in consulated for visa cancel';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 1;
				$timeline->save();

				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 0;
				// $stage_f->waiting_for_med_fit = 0;
				// $stage_f->waiting_for_pass = 0;
				// $stage_f->waiting_for_visa = 0;
				// $stage_f->hold = 0;
				// $stage_f->required_docs = 0;
				// $stage_f->ready_for_docu = 0;
				// $stage_f->pass_ready_for_submi = 0;
				// $stage_f->submitted_cons = 0;
				// $stage_f->visa_stamp_rejection = 0;
				// $stage_f->wait_for_emi_apll = 0;
				// $stage_f->completed = 0;
				// $stage_f->ready_for_visa_can = 0;
				// $stage_f->sub_in_cons_for_visa_can = 1;
				// $stage_f->visa_canceled = 0;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// $status_f->visa_canceled = 0;
				// $status_f->process_completed = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

			}
			// Visa Canceled
			if($visa_cancel_final == 2){
				// Update in Service Details
				$ser_details->visa_canceled_final = $visa_cancel_final;
				$ser_details->save();
				// Updated in Service Status
				$ser_status->visa_canceled_final = 1;
				$ser_status->save();
				// Updated in Candidate Status

				$cand_status->active_status = 'Visa canceled';
				$cand_status->active_sub_status = "Visa cancel";
				$cand_status->incomplete_status = '';
				$cand_status->save();

				$chk_vcn = CandCompFinalStatus::where('final_status','=','Visa cancel')->where('cand_id','=',$cand_id)->first();
				$chk_vs = CandCompFinalStatus::where('final_status','=','Visa Stamped')->where('cand_id','=',$cand_id)->first();

				if($chk_vcn == ''){
					$finalStatus->cand_id = $cand_id;
					$finalStatus->final_status = "Visa cancel";
					$finalStatus->save();
				}

				if(isset($chk_vs)){
					$chk_vs->delete();
				}

				// $stage_f->new_document = 0;
				// $stage_f->waiting_for_musaned_reg = 0;
				// $stage_f->waiting_for_dele_from_another_office = 0;
				// $stage_f->waiting_for_mofa = 0;
				// $stage_f->mofa_unpaid = 0;
				// $stage_f->waiting_for_med_fit = 0;
				// $stage_f->waiting_for_pass = 0;
				// $stage_f->waiting_for_visa = 0;
				// $stage_f->hold = 0;
				// $stage_f->required_docs = 0;
				// $stage_f->ready_for_docu = 0;
				// $stage_f->pass_ready_for_submi = 0;
				// $stage_f->submitted_cons = 0;
				// $stage_f->visa_stamp_rejection = 0;
				// $stage_f->wait_for_emi_apll = 0;
				// $stage_f->completed = 0;
				// $stage_f->ready_for_visa_can = 0;
				// $stage_f->sub_in_cons_for_visa_can = 0;
				// $stage_f->visa_canceled = 1;
				// $stage_f->user_id = Auth::user()->user_id;
				// $stage_f->save();

				// $status_f->visa_canceled = 1;
				// $status_f->process_completed = 0;
				// $status_f->user_id = Auth::user()->user_id;
				// $status_f->save();

				// check and remove visa cancel status
				$status = explode(",",$cand_compl->c_status);
				$rem_status = ["Visa Stamped"];
				$new_array = array_diff($status,$rem_status);
				$cand_compl->c_status = implode(",",$new_array);
				$cand_compl->save();

				// created Timeline
				date_default_timezone_set('Asia/Kolkata');
				$timeline = new Timeline();
				$timeline->cand_id = $cand_id;
				$timeline->subject = 'Visa Canceled';
				$timeline->time = date("h:i:sa");
				$timeline->user_id = Auth::user()->user_id;
				$timeline->status = 1;
				$timeline->save();
			}

			Session::flash('success','Visa Cancel Updated');
			return redirect('services/candidate-status/' . $cand_id);

		}else{
			Session::flash('error','Please Select Any Option');
			return redirect()->back();
		}

	}

	public function getCountC(Request $request){
		$user_id = $request->user_id;
		$br_id = $request->br_id;
		if($user_id != '' && $br_id != ''){
			$courC = Courier::where('stage','=','Courier Order')->where('user_id','=',$user_id)->where('br_id','=',$br_id)->count();
			$disC = Courier::where('stage','=','Dispatched')->where('user_id','=',$user_id)->where('br_id','=',$br_id)->count();
			$ongC = Courier::where('stage','=','Ongoing')->where('user_id','=',$user_id)->where('br_id','=',$br_id)->count();
			$recC = Courier::where('stage','=','Received')->where('user_id','=',$user_id)->where('br_id','=',$br_id)->count();
			$delC = Courier::where('stage','=','Delivered')->where('user_id','=',$user_id)->where('br_id','=',$br_id)->count();
			$holdC = Courier::where('stage','=','Hold')->where('user_id','=',$user_id)->where('br_id','=',$br_id)->count();
		}elseif($user_id != '' && $br_id == ''){
			$courC = Courier::where('stage','=','Courier Order')->where('user_id','=',$user_id)->count();
			$disC = Courier::where('stage','=','Dispatched')->where('user_id','=',$user_id)->count();
			$ongC = Courier::where('stage','=','Ongoing')->where('user_id','=',$user_id)->count();
			$recC = Courier::where('stage','=','Received')->where('user_id','=',$user_id)->count();
			$delC = Courier::where('stage','=','Delivered')->where('user_id','=',$user_id)->count();
			$holdC = Courier::where('stage','=','Hold')->where('user_id','=',$user_id)->count();
		}elseif($user_id == '' && $br_id != ''){
			$courC = Courier::where('stage','=','Courier Order')->where('br_id','=',$br_id)->count();
			$disC = Courier::where('stage','=','Dispatched')->where('br_id','=',$br_id)->count();
			$ongC = Courier::where('stage','=','Ongoing')->where('br_id','=',$br_id)->count();
			$recC = Courier::where('stage','=','Received')->where('br_id','=',$br_id)->count();
			$delC = Courier::where('stage','=','Delivered')->where('br_id','=',$br_id)->count();
			$holdC = Courier::where('stage','=','Hold')->where('br_id','=',$br_id)->count();
		}else{
			$courC = Courier::where('stage','=','Courier Order')->count();
			$disC = Courier::where('stage','=','Dispatched')->count();
			$ongC = Courier::where('stage','=','Ongoing')->count();
			$recC = Courier::where('stage','=','Received')->count();
			$delC = Courier::where('stage','=','Delivered')->count();
			$holdC = Courier::where('stage','=','Hold')->count();			
		}

		$data = [
			'courC' => $courC,
			'disC' => $disC,
			'ongC' => $ongC,
			'recC' => $recC,
			'delC' => $delC,
			'holdC' => $holdC,
		];

		return response()->json($data);
	}

	// Courier Section Start
	
	public function courier_list(){
		$pageConfigs = ['pageHeader' => false];

		if(Auth::user()->user_type == 1){
			$courC = Courier::where('stage','=','Courier Order')->count();
			$disC = Courier::where('stage','=','Dispatched')->count();
			$ongC = Courier::where('stage','=','Ongoing')->count();
			$recC = Courier::where('stage','=','Received')->count();
			$delC = Courier::where('stage','=','Delivered')->count();
			$holdC = Courier::where('stage','=','Hold')->count();
		}else{
			$courC = Courier::where('stage','=','Courier Order')->where('user_id','=',Auth::user()->user_id)->count();
			$disC = Courier::where('stage','=','Dispatched')->where('user_id','=',Auth::user()->user_id)->count();
			$ongC = Courier::where('stage','=','Ongoing')->where('user_id','=',Auth::user()->user_id)->count();
			$recC = Courier::where('stage','=','Received')->where('user_id','=',Auth::user()->user_id)->count();
			$delC = Courier::where('stage','=','Delivered')->where('user_id','=',Auth::user()->user_id)->count();
			$holdC = Courier::where('stage','=','Hold')->where('user_id','=',Auth::user()->user_id)->count();
		}
		return view('content.service.courier', ['pageConfigs' => $pageConfigs,'courC' => $courC,'disC' => $disC,'ongC' => $ongC,'recC' => $recC,'delC' => $delC,'holdC' => $holdC]);
	}

	public function courier_view($id){
		$post = DB::table('qr_courier_tbl as courier')
			->leftjoin('qr_party_tbl as party','party.pty_id','=','courier.pty_id')
			->leftjoin('qr_courier_ser_name_tbl as csn','csn.id','=','courier.csn_id')
			->leftjoin('users as userC','userC.user_id','=','courier.care_of_id')
			->leftjoin('users as userD','userD.user_id','=','courier.user_id')
			->select('courier.*','party.pty_ag_name','csn.name as csname','userC.name as cname','userD.name as Dname')
			->where('courier.id','=',$id)
			->first();
		$pageConfigs = ['pageHeader' => false];
		return view('content.service.courierview',['pageConfigs' => $pageConfigs,'post' => $post]);
	}

	Public function courier_list_json(){

		$perms = AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();
		if(Auth::user()->user_type == 1 || (isset($perms) && $perms->full_access == 1)){

			$cand_cour = DB::table('qr_courier_tbl as courier')
			->leftjoin('qr_courier_status_tbl as cors','courier.last_clone_id','=','cors.courier_id')
			->leftjoin('qr_courier_sd_tbl as cord','courier.last_clone_id','=','cord.courier_id')
			->leftjoin('qr_party_tbl as party','party.pty_id','=','courier.pty_id')
			->select('courier.*','party.pty_ag_name','cors.stage as stgname','cors.status as cstatus')
			->get();

		}elseif (Auth::user()->user_type == 2 || (isset($perms) && $perms->full_access == 0)){
			if($perms->ser_courier_view == 1){
				$cand_cour = DB::table('qr_courier_tbl as courier')
				->leftjoin('qr_courier_status_tbl as cors','courier.last_clone_id','=','cors.courier_id')
				->leftjoin('qr_courier_sd_tbl as cord','courier.last_clone_id','=','cord.courier_id')
				->leftjoin('qr_party_tbl as party','party.pty_id','=','courier.pty_id')
				->select('courier.*','party.pty_ag_name','cors.stage as stgname','cors.status as cstatus')
				->get();
			}else{
				$cand_cour = DB::table('qr_courier_tbl as courier')
				->leftjoin('qr_courier_status_tbl as cors','courier.last_clone_id','=','cors.courier_id')
				->leftjoin('qr_courier_sd_tbl as cord','courier.last_clone_id','=','cord.courier_id')
				->leftjoin('qr_party_tbl as party','party.pty_id','=','courier.pty_id')
				->select('courier.*','party.pty_ag_name','cors.stage as stgname','cors.status as cstatus')
				->where('courier.user_id','=',Auth::user()->user_id)
				->orWhere('courier.care_of_id','=',Auth::user()->user_id)
				->get();
			}
		}

		// Old Query
		// $cand_cour = DB::table('qr_courier_tbl as courier')
		// ->leftjoin('qr_courier_status_tbl as cors','courier.last_clone_id','=','cors.courier_id')
		// ->leftjoin('qr_courier_sd_tbl as cord','courier.last_clone_id','=','cord.courier_id')
		// ->leftjoin('qr_party_tbl as party','party.pty_id','=','courier.pty_id')
		// ->select('courier.*','party.pty_ag_name','cors.stage as stgname','cors.status as cstatus')
		// ->get();

		// New Query
		// $cand_cour = DB::table('qr_courier_tbl as courier')
		// ->leftjoin('courierclones as cclone','courier.id','=','cclone.courier_id')
		// ->leftjoin('qr_courier_status_tbl as cors','cclone.id','=','cors.courier_id')
		// ->leftjoin('qr_courier_sd_tbl as cord','cclone.id','=','cord.courier_id')
		// ->leftjoin('qr_party_tbl as party','party.pty_id','=','cclone.pty_id')
		// ->select('courier.*','party.pty_ag_name','cors.stage','cors.status as cstatus','cclone.id as cid')
		// ->where('cclone.status','=','0')
		// ->get();

		// $cand_cour = CandCourier::all();

		// $data['data'] = $cand_cour;
		// return response()->json($data);
		return DataTables::of($cand_cour)->toJson();
	}

	public function getCandDetails(Request $request){
		$pass_no = $request->input('pass_no');
		$check_cr = Courier::where('pass_no','=',$pass_no)->count();
		$check_cn = Candidate::where('cand_passport_no','=',$pass_no)->count();
		// $chk_cand = DB::table('qr_candidate_tbl as cand')
		// ->leftjoin('qr_party_tbl as party','cand.pty_id','=','party.pty_id')
		// ->leftjoin('users as user','user.user_id','=','cand.care_of_id')
		// ->select('cand.cand_id','cand.cand_fname','cand.cand_lname','cand.cand_mname','cand.pty_id','cand.care_of_id','user.name','party.pty_ag_name')
		// ->where('cand.cand_passport_no','=',$pass_no)
		// ->first();
		// $data['data'] = $chk_cand;

		if($check_cn > 0 && $check_cr > 0){
			$chk_cand = DB::table('qr_candidate_tbl as cand')
			->leftjoin('qr_party_tbl as party','cand.pty_id','=','party.pty_id')
			->leftjoin('users as user','user.user_id','=','cand.care_of_id')
			->leftjoin('qr_courier_tbl as courier','cand.cand_passport_no','=','courier.pass_no')
			->leftjoin('qr_courier_sd_tbl as courier_sd','cand.cand_passport_no','courier_sd.pass_no')
			->leftjoin('qr_courier_status_tbl as courier_st','cand.cand_passport_no','courier_st.pass_no')
			->select('cand.cand_id as candID','cand.cand_fname as candFname','cand.cand_lname as candLname','cand.pty_id as candPty','cand.care_of_id as careOFID','user.name as uname','party.pty_ag_name','courier.*','courier_st.stage as stgname')
			->where('cand.cand_passport_no','=',$pass_no)
			->first();
			
		}elseif($check_cn > 0 && $check_cr == 0){
			$chk_cand = DB::table('qr_candidate_tbl as cand')
			->leftjoin('qr_party_tbl as party','cand.pty_id','=','party.pty_id')
			->leftjoin('users as user','user.user_id','=','cand.care_of_id')
			->select('cand.cand_id as candID','cand.cand_fname as candFname','cand.cand_lname as candLname','cand.cand_mname as candMnane','cand.pty_id as candPty','cand.care_of_id as careOFID','user.name as uname','party.pty_ag_name')
			->where('cand.cand_passport_no','=',$pass_no)
			->first();
			
		}elseif($check_cr > 0 && $check_cn == 0){

			$chk_cand = DB::table('qr_courier_tbl as courier')
			->leftjoin('qr_courier_sd_tbl as courier_sd','courier.id','=','courier_sd.courier_id')
			->leftjoin('qr_courier_status_tbl as courier_st','courier.id','=','courier_st.courier_id')
			->leftjoin('qr_party_tbl as party','courier.pty_id','=','party.pty_id')
			->select('courier.*','courier_st.stage as stgname','party.pty_ag_name')
			->where('courier.pass_no','=',$pass_no)
			->first();
			
		}else{
			$chk_cand = DB::table('qr_candidate_tbl as cand')
				->leftjoin('qr_party_tbl as party','cand.pty_id','=','party.pty_id')
				->leftjoin('users as user','user.user_id','=','cand.care_of_id')
				->select('cand.cand_id','cand.cand_fname','cand.cand_lname','cand.cand_mname','cand.pty_id','cand.care_of_id','user.name','party.pty_ag_name')
				->where('cand.cand_passport_no','=',$pass_no)
				->first();
		}



		$data['data'] = $chk_cand;
		

		if($chk_cand){
			return response()->json($chk_cand);
		}else{
			return response()->json('');
		}

		
	}

	public function getCandDetails2(Request $request){
		$pass_no = $request->input('pass_no');
		$check_cr = Courierclone::where('pass_no','=',$pass_no)->count();
		$check_cn = Candidate::where('cand_passport_no','=',$pass_no)->count();
		$check_crc = Crmcpass::where('pass_no','=',$pass_no)->count();
		// $chk_cand = DB::table('qr_candidate_tbl as cand')
		// ->leftjoin('qr_party_tbl as party','cand.pty_id','=','party.pty_id')
		// ->leftjoin('users as user','user.user_id','=','cand.care_of_id')
		// ->select('cand.cand_id','cand.cand_fname','cand.cand_lname','cand.cand_mname','cand.pty_id','cand.care_of_id','user.name','party.pty_ag_name')
		// ->where('cand.cand_passport_no','=',$pass_no)
		// ->first();
		// $data['data'] = $chk_cand;

		// if($check_cn > 0 && $check_cr > 0){
		// 	$chk_cand = DB::table('qr_candidate_tbl as cand')
		// 	->leftjoin('qr_party_tbl as party','cand.pty_id','=','party.pty_id')
		// 	->leftjoin('users as user','user.user_id','=','cand.care_of_id')
		// 	->leftjoin('qr_courier_tbl as courier','cand.cand_passport_no','=','courier.pass_no')
		// 	->leftjoin('qr_courier_sd_tbl as courier_sd','courier.last_clone_id','courier_sd.courier_id')
		// 	->leftjoin('qr_courier_status_tbl as courier_st','courier.last_clone_id','courier_st.courier_id')
		// 	->select('cand.cand_id as candID','cand.cand_fname as candFname','cand.cand_lname as candLname','cand.pty_id as candPty','cand.care_of_id as careOFID','user.name as uname','party.pty_ag_name','courier.*','courier_st.stage as stgname')
		// 	->where('cand.cand_passport_no','=',$pass_no)
		// 	->first();
			
		// }elseif($check_cn > 0 && $check_cr == 0){
		// 	$chk_cand = DB::table('qr_candidate_tbl as cand')
		// 	->leftjoin('qr_party_tbl as party','cand.pty_id','=','party.pty_id')
		// 	->leftjoin('users as user','user.user_id','=','cand.care_of_id')
		// 	->select('cand.cand_id as candID','cand.cand_fname as candFname','cand.cand_lname as candLname','cand.cand_mname as candMnane','cand.pty_id as candPty','cand.care_of_id as careOFID','user.name as uname','party.pty_ag_name')
		// 	->where('cand.cand_passport_no','=',$pass_no)
		// 	->first();
			
		// }elseif($check_cr > 0 && $check_cn == 0){

		// 	$chk_cand = DB::table('qr_courier_tbl as courier')
		// 	->leftjoin('qr_courier_sd_tbl as courier_sd','courier.last_clone_id','=','courier_sd.courier_id')
		// 	->leftjoin('qr_courier_status_tbl as courier_st','courier.last_clone_id','=','courier_st.courier_id')
		// 	->leftjoin('qr_party_tbl as party','courier.pty_id','=','party.pty_id')
		// 	->select('courier.*','courier_st.stage as stgname','party.pty_ag_name')
		// 	->where('courier.pass_no','=',$pass_no)
		// 	->first();
			
		// }else{
		// 	$chk_cand = DB::table('qr_candidate_tbl as cand')
		// 		->leftjoin('qr_party_tbl as party','cand.pty_id','=','party.pty_id')
		// 		->leftjoin('users as user','user.user_id','=','cand.care_of_id')
		// 		->select('cand.cand_id','cand.cand_fname','cand.cand_lname','cand.cand_mname','cand.pty_id','cand.care_of_id','user.name','party.pty_ag_name')
		// 		->where('cand.cand_passport_no','=',$pass_no)
		// 		->first();
		// }

		if($check_cr > 0 && $check_cn > 0 && $check_crc > 0){
			$chk_cand = DB::table('qr_courier_tbl as courier')
			->leftjoin('qr_candidate_tbl as cand','cand.cand_passport_no','=','courier.pass_no')
			->leftjoin('crmcpasses as crmpass','crmpass.pass_no','=','courier.pass_no')
			->leftjoin('qr_courier_sd_tbl as courier_sd','courier_sd.courier_id','=','courier.last_clone_id')
			->leftjoin('qr_courier_status_tbl as courier_st','courier_st.courier_id','=','courier.last_clone_id')
			->leftjoin('qr_party_tbl as party','party.pty_id','=','courier.pty_id')
			->leftjoin('users as user','user.user_id','=','courier.care_of_id')
			->select('courier.*','cand.cand_id as CandID','crmpass.crmc_id as CrmcID','cand.cand_fname as candFname','cand.cand_lname as candLname','cand.pty_id as candPty','cand.care_of_id as careOFID','user.name as uname','party.pty_ag_name','courier_st.stage as stgname')
			->where('courier.pass_no','=',$pass_no)
			->first();
		}elseif($check_cr > 0 && $check_cn > 0 && $check_crc == 0){
			$chk_cand = DB::table('qr_courier_tbl as courier')
			->leftjoin('qr_candidate_tbl as cand','cand.cand_passport_no','=','courier.pass_no')
			->leftjoin('qr_courier_sd_tbl as courier_sd','courier_sd.courier_id','=','courier.last_clone_id')
			->leftjoin('qr_courier_status_tbl as courier_st','courier_st.courier_id','=','courier.last_clone_id')
			->leftjoin('qr_party_tbl as party','party.pty_id','=','courier.pty_id')
			->leftjoin('users as user','user.user_id','=','courier.care_of_id')
			->select('courier.*','cand.cand_id as CandID','cand.cand_fname as candFname','cand.cand_lname as candLname','cand.pty_id as candPty','cand.care_of_id as careOFID','user.name as uname','party.pty_ag_name','courier_st.stage as stgname')
			->where('courier.pass_no','=',$pass_no)
			->first();
		}elseif($check_cr > 0 && $check_cn == 0 && $check_crc == 0){
			$chk_cand = DB::table('qr_courier_tbl as courier')
			->leftjoin('qr_courier_sd_tbl as courier_sd','courier_sd.courier_id','=','courier.last_clone_id')
			->leftjoin('qr_courier_status_tbl as courier_st','courier_st.courier_id','=','courier.last_clone_id')
			->leftjoin('qr_party_tbl as party','party.pty_id','=','courier.pty_id')
			->leftjoin('users as user','user.user_id','=','courier.care_of_id')
			->select('courier.*','user.name as uname','party.pty_ag_name','courier_st.stage as stgname')
			->where('courier.pass_no','=',$pass_no)
			->first();
		}elseif($check_cr == 0 && $check_cn > 0 && $check_crc > 0){
			$chk_cand = DB::table('qr_candidate_tbl as cand')
			->leftjoin('crmcpasses as crmpass','crmpass.pass_no','=','cand.cand_passport_no')
			->leftjoin('qr_party_tbl as party','party.pty_id','=','cand.pty_id')
			->leftjoin('users as user','user.user_id','=','cand.care_of_id')
			->select('cand.cand_id as CandID','cand.cand_fname as candFname','cand.cand_lname as candLname','cand.pty_id as candPty','cand.care_of_id as careOFID','crmpass.crmc_id as CrmcID','user.name as uname','party.pty_ag_name')
			->where('cand.cand_passport_no','=',$pass_no)
			->first();

		}elseif($check_cr == 0 && $check_cn > 0 && $check_crc == 0){
			$chk_cand = DB::table('qr_candidate_tbl as cand')
			->leftjoin('qr_party_tbl as party','party.pty_id','=','cand.pty_id')
			->leftjoin('users as user','user.user_id','=','cand.care_of_id')
			->select('cand.cand_id as CandID','cand.cand_fname as candFname','cand.cand_mname as candMnane','cand.cand_lname as candLname','cand.pty_id as candPty','cand.care_of_id as careOFID')
			->where('cand.cand_passport_no','=',$pass_no)
			->first();
		}elseif($check_cr > 0 && $check_cn == 0 && $check_crc > 0){
			$chk_cand = DB::table('qr_courier_tbl as courier')
			->leftjoin('crmcpasses as crmpass','crmpass.pass_no','=','courier.pass_no')
			->leftjoin('qr_courier_sd_tbl as courier_sd','courier_sd.courier_id','=','courier.last_clone_id')
			->leftjoin('qr_courier_status_tbl as courier_st','courier_st.courier_id','=','courier.last_clone_id')
			->leftjoin('qr_party_tbl as party','party.pty_id','=','courier.pty_id')
			->leftjoin('users as user','user.user_id','=','courier.care_of_id')
			->select('courier.*','crmpass.crmc_id as CrmcID','user.name as uname','party.pty_ag_name','courier_st.stage as stgname')
			->where('courier.pass_no','=',$pass_no)
			->first();
		}elseif($check_cr == 0 && $check_cn == 0 && $check_crc > 0){
			$chk_cand = DB::table('crmcpasses as crmpass')
			->leftjoin('crm_candidates as crmcand','crmcand.id','=','crmpass.crmc_id')
			->leftjoin('users as user','user.user_id','=','crmcand.careoff_id')
			->select('crmpass.crmc_id as CrmcID','crmpass.fname as crmfname','crmpass.lname as crmlname','crmpass.mname as crmmname','crmcand.careoff_id as CareoffID')
			->where('crmpass.pass_no','=',$pass_no)
			->first();
		}else{
			$chk_cand = DB::table('qr_candidate_tbl as cand')
			->leftjoin('qr_party_tbl as party','cand.pty_id','=','party.pty_id')
			->leftjoin('users as user','user.user_id','=','cand.care_of_id')
			->select('cand.cand_id','cand.cand_fname','cand.cand_lname','cand.cand_mname','cand.pty_id','cand.care_of_id','user.name','party.pty_ag_name')
			->where('cand.cand_passport_no','=',$pass_no)
			->first();
		}

		$data['data'] = $chk_cand;
		

		if($chk_cand){
			return response()->json($chk_cand);
		}else{
			return response()->json('');
		}

		
	}

	public function courierStore(Request $request){
		
		$pass_no = $request->input('pass_no');
		$cand = Candidate::where('cand_passport_no','=',$pass_no)->first();
		$courier_mode = $request->courier_mode;

		// Get staff brach
		if(Auth::user()->user_type != 1){
			$staff_br = DB::table('qr_staff_tbl')->where('staff_id','=',Auth::user()->user_id)->first();
			$branch = $staff_br->staff_branch;
		}else{
			$branch = $request->cand_from;
		}

		// Add Courier
		$courier = new Courier();
		if(isset($cand)){
			$courier->cand_id = $cand->cand_id;
		}else{
			$courier->cand_id = '';
		}

		$courier->pass_no = $pass_no;
		$courier->pty_id = $request->pty_id;

		$courier->cand_fname = $request->cand_fname;
		$courier->cand_mname = $request->cand_mname;
		$courier->cand_lname = $request->cand_lname;

		if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
			$courier->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
		}else{
			$courier->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
		}

		$courier->courier_from = $request->cand_from;
		if($request->pty_id != '200'){
			$courier->courier_to = $request->cand_to;
		}else{	
			$courier->courier_to_add = $request->cand_to_add;
		}

		$courier->valid_pass = $request->valid_passport;
		$courier->old_pass = $request->old_passport;
		$courier->driving_lic = $request->driving_license;
		$courier->exit_paper = $request->exit_paper;
		$courier->photo_r = $request->photo;

		$courier->med_rep = $request->medical_report;
		$courier->other_doc = $request->other_documents;
		$courier->doc_name = $request->documents_name;
		$courier->care_of_id = $request->care_of_id ? $request->care_of_id : '';
		$courier->user_id = Auth::user()->user_id;
		$courier->csn_id = $request->cs_id;
		$courier->courier_mode = $request->courier_mode;
		$courier->br_id = $branch;

		if ($request->groupid == 'none') {
			$courier->groupid = time();
		} else {
			$courier->groupid = $request->groupid;
		}

		$courier->courier_mode = $courier_mode;

		$courier->save();

		// Update in Courier Status and Courier Details
		$last_id = $courier->id;

		// Update in clone
		$courier2 = new Courierclone();
		$courier2->courier_id = $last_id;
		if(isset($cand)){
			$courier2->cand_id = $cand->cand_id;
		}else{
			$courier2->cand_id = '';
		}
		$courier2->pty_id = $request->pty_id;
		$courier2->pass_no = $pass_no;
		$courier2->cand_fname = $request->cand_fname;
		$courier2->cand_mname = $request->cand_mname;
		$courier2->cand_lname = $request->cand_lname;
		if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
			$courier2->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
		}else{
			$courier2->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
		}
		$courier2->care_of_id = $request->care_of_id ? $request->care_of_id : '';
		$courier2->courier_from = $request->cand_from;
		if($request->pty_id != '200'){
			$courier2->courier_to = $request->cand_to;
		}else{	
			$courier2->courier_to_add = $request->cand_to_add;
		}

		$courier2->valid_pass = $request->valid_passport;
		$courier2->old_pass = $request->old_passport;
		$courier2->driving_lic = $request->driving_license;
		$courier2->exit_paper = $request->exit_paper;
		$courier2->photo_r = $request->photo;

		$courier2->med_rep = $request->medical_report;
		$courier2->other_doc = $request->other_documents;
		$courier2->doc_name = $request->documents_name;

		$courier2->csn_id = $request->cs_id;
		$courier2->user_id = Auth::user()->user_id;
		if($request->courier_mode == '0'){
			$courier2->in_out = '0';
		}else{
			$courier2->in_out = '1';
		}
		$courier2->br_id = $branch;
	
		$courier2->groupid = $courier->groupid;

		if($request->courier_mode == '0'){
			$courier2->stage = 'Courier Order';
		}else{
			$courier2->stage = 'Received';
		}
		$courier2->stage = 'Courier Order';
		$courier2->save();

		// Get Last Clone ID

		$getCloneID = $courier2->id;

		$courier_status = new CourierStatus();
		$courier_detail = new CourierDetails();

		$courier_status->courier_id = $getCloneID;
		$courier_status->pass_no = $pass_no;
		if($request->courier_mode == '0'){
			$courier_status->stage = 'Courier Order';
		}else{
			$courier_status->stage = 'Received';
		}

		$courier_status->user_id = Auth::user()->user_id;
		$courier_status->save();

		$courier_detail->courier_id = $getCloneID;
		$courier_detail->pass_no = $pass_no;
		$courier_detail->user_id = Auth::user()->user_id;
		$courier_detail->save();

		if($request->courier_mode == '0'){
			Session::flash('success', 'Courier Order Add Successfully');
		}else{
			Session::flash('success', 'Courier received Successfully');
		}
	
		return redirect()->back();


		// $courier->in_out = $request->in_out;
		// if($request->in_out == '0'){
			// $courier->br_id = $branch;
		// }
		// $courier->courier_type = $request->courier_type;
		// if($request->courier_type == 'group'  && $request->groupid == 'none'){
		// 	$courier->groupid = 'qamr'.time();
		// }else{
		// 	$courier->groupid = $request->groupid;
		// }

		// update in Courier

	

	}

	public function courierStore2(Request $request){
		
		$pass_no = $request->input('pass_no');
		$cand = Candidate::where('cand_passport_no','=',$pass_no)->first();
		$courier_mode = $request->courier_mode;

		// Get staff brach
		if(Auth::user()->user_type != 1){
			$staff_br = DB::table('qr_staff_tbl')->where('staff_id','=',Auth::user()->user_id)->first();
			$branch = $staff_br->staff_branch;
		}else{
			$branch = $request->cand_from;
		}

		dd($request);

		// check if Courier is exist in Courier Notification
		$checkCR = Couriernotification::where('pass_no','=',$pass_no)->where('status','=',0)->first();
		// dd($checkCR);
		if(isset($checkCR)){
			// check Courier based on passport and Update it
			$post = Courier::where('pass_no','=',$pass_no)->first();

			if(isset($cand)){
				$post->cand_id = $cand->cand_id;
			}else{
				$post->cand_id = '';
			}

			$post->pty_id = $request->pty_id;

			$post->courier_from = $request->cand_from;
			if($request->pty_id != '200'){
				$post->courier_to = $request->cand_to;
			}else{	
				$post->courier_to_add = $request->cand_to_add;
			}

			$post->valid_pass = $request->valid_passport;
			$post->old_pass = $request->old_passport;
			$post->driving_lic = $request->driving_license;
			$post->exit_paper = $request->exit_paper;
			$post->photo_r = $request->photo;

			$post->med_rep = $request->medical_report;
			$post->other_doc = $request->other_documents;
			$post->doc_name = $request->documents_name;
			$post->care_of_id = $request->care_of_id ? $request->care_of_id : '';
			$post->user_id = Auth::user()->user_id;
			$post->csn_id = $request->cs_id;
			$post->br_id = $branch;

			if ($request->groupid == 'none') {
				$post->groupid = 'qamr'.time();
			} else {
				$post->groupid = $request->groupid;
			}
			
			if($request->courier_mode == '0'){
				$post->stage = 'Courier Order';
				$post->in_out = 'out';
			}else{
				$post->stage = 'Received';
				$post->in_out = 'in';
			}
			
			$post->courier_mode = $courier_mode;
			

			$post->save();

			// Update in Courier Status and Courier Details
			$last_id = $post->id;

			// Update in clone
			$courier2 = new Courierclone();
			$courier2->courier_id = $last_id;
			if(isset($cand)){
				$courier2->cand_id = $cand->cand_id;
			}else{
				$courier2->cand_id = '';
			}

			$courier2->pty_id = $request->pty_id;
			$courier2->pass_no = $pass_no;
			$courier2->cand_fname = $request->cand_fname;
			$courier2->cand_mname = $request->cand_mname;
			$courier2->cand_lname = $request->cand_lname;
			if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
				$courier2->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
			}else{
				$courier2->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
			}
			$courier2->care_of_id = $request->care_of_id ? $request->care_of_id : '';
			$courier2->courier_from = $request->cand_from;
			if($request->pty_id != '200'){
				$courier2->courier_to = $request->cand_to;
			}else{	
				$courier2->courier_to_add = $request->cand_to_add;
			}

			$courier2->valid_pass = $request->valid_passport;
			$courier2->old_pass = $request->old_passport;
			$courier2->driving_lic = $request->driving_license;
			$courier2->exit_paper = $request->exit_paper;
			$courier2->photo_r = $request->photo;

			$courier2->med_rep = $request->medical_report;
			$courier2->other_doc = $request->other_documents;
			$courier2->doc_name = $request->documents_name;

			$courier2->csn_id = $request->cs_id;
			$courier2->user_id = Auth::user()->user_id;

			
			$courier2->br_id = $branch;
	
			if ($request->groupid == 'none') {
				$courier2->groupid = 'qamr'.time();
			} else {
				$courier2->groupid = $request->groupid;
			}

	
			if($courier_mode == '0'){
				$courier2->stage = 'Courier Order';
				$courier2->in_out = 'out';
			}else{
				$courier2->stage = 'Received';
				$courier2->in_out = 'in';
			}
			$courier2->save();



		}else{

			// Add Courier
			$courier = new Courier();
			if(isset($cand)){
				$courier->cand_id = $cand->cand_id;
			}else{
				$courier->cand_id = '';
			}

			$courier->pass_no = $pass_no;
			$courier->pty_id = $request->pty_id;

			$courier->cand_fname = $request->cand_fname;
			$courier->cand_mname = $request->cand_mname;
			$courier->cand_lname = $request->cand_lname;

			if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
				$courier->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
			}else{
				$courier->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
			}

			$courier->courier_from = $request->cand_from;
			if($request->pty_id != '200'){
				$courier->courier_to = $request->cand_to;
			}else{	
				$courier->courier_to_add = $request->cand_to_add;
			}

			$courier->valid_pass = $request->valid_passport;
			$courier->old_pass = $request->old_passport;
			$courier->driving_lic = $request->driving_license;
			$courier->exit_paper = $request->exit_paper;
			$courier->photo_r = $request->photo;

			$courier->med_rep = $request->medical_report;
			$courier->other_doc = $request->other_documents;
			$courier->doc_name = $request->documents_name;
			$courier->care_of_id = $request->care_of_id ? $request->care_of_id : '';
			$courier->user_id = Auth::user()->user_id;
			$courier->csn_id = $request->cs_id;
			$courier->courier_mode = $courier_mode;
			$courier->br_id = $branch;

			if ($request->groupid == 'none') {
				$courier->groupid = 'qamr'.time();
			} else {
				$courier->groupid = $request->groupid;
			}
			
			if($courier_mode == '0'){
				$courier->stage = 'Courier Order';
				$courier->in_out = 'out';
			}else{
				$courier->stage = 'Received';
				$courier->in_out = 'in';
			}
	
			$courier->save();

			// Update in Courier Status and Courier Details
			$last_id = $courier->id;

			// Update in clone
			$courier2 = new Courierclone();
			$courier2->courier_id = $last_id;
			if(isset($cand)){
				$courier2->cand_id = $cand->cand_id;
			}else{
				$courier2->cand_id = '';
			}

			$courier2->pty_id = $request->pty_id;
			$courier2->pass_no = $pass_no;
			$courier2->cand_fname = $request->cand_fname;
			$courier2->cand_mname = $request->cand_mname;
			$courier2->cand_lname = $request->cand_lname;
			if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
				$courier2->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
			}else{
				$courier2->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
			}
			$courier2->care_of_id = $request->care_of_id ? $request->care_of_id : '';
			$courier2->courier_from = $request->cand_from;
			if($request->pty_id != '200'){
				$courier2->courier_to = $request->cand_to;
			}else{	
				$courier2->courier_to_add = $request->cand_to_add;
			}

			$courier2->valid_pass = $request->valid_passport;
			$courier2->old_pass = $request->old_passport;
			$courier2->driving_lic = $request->driving_license;
			$courier2->exit_paper = $request->exit_paper;
			$courier2->photo_r = $request->photo;

			$courier2->med_rep = $request->medical_report;
			$courier2->other_doc = $request->other_documents;
			$courier2->doc_name = $request->documents_name;

			$courier2->csn_id = $request->cs_id;
			$courier2->user_id = Auth::user()->user_id;


			$courier2->br_id = $branch;
	
			$courier2->groupid = $courier->groupid;
	
			if($courier_mode == '0'){
				$courier2->stage = 'Courier Order';
				$courier2->in_out = 'out';
			}else{
				$courier2->stage = 'Received';
				$courier2->in_out = 'in';
			}
			$courier2->save();

		}

		
		// Get Last Clone ID

		$getCloneID = $courier2->id;

		$courier_status = new CourierStatus();
		$courier_detail = new CourierDetails();

		$courier_status->courier_id = $getCloneID;
		$courier_status->pass_no = $pass_no;
		if($courier_mode == '0'){
			$courier_status->stage = 'Courier Order';
		}else{
			$courier_status->stage = 'Received';
		}

		$courier_status->user_id = Auth::user()->user_id;
		$courier_status->save();

		$courier_detail->courier_id = $getCloneID;
		$courier_detail->pass_no = $pass_no;
		$courier_detail->user_id = Auth::user()->user_id;
		$courier_detail->save();

		if($request->courier_mode == '0'){
			Session::flash('success', 'Courier Order Add Successfully');
		}else{
			Session::flash('success', 'Courier received Successfully');
		}
	
		return redirect()->back();


		// $courier->in_out = $request->in_out;
		// if($request->in_out == '0'){
			// $courier->br_id = $branch;
		// }
		// $courier->courier_type = $request->courier_type;
		// if($request->courier_type == 'group'  && $request->groupid == 'none'){
		// 	$courier->groupid = 'qamr'.time();
		// }else{
		// 	$courier->groupid = $request->groupid;
		// }

		// update in Courier

	

	}

	public function courierStore3(Request $request){
		$pass_no = $request->pass_no;
		$courier_mode = $request->courier_mode;
		$pty_id = $request->pty_id;
		$cand = Candidate::where('cand_passport_no','=',$pass_no)->first();

		// Get staff brach
		if(Auth::user()->user_type != 1){
			$staff_br = DB::table('qr_staff_tbl')->where('staff_id','=',Auth::user()->user_id)->first();
			$branch = $staff_br->staff_branch;
		}else{
			$branch = $request->cand_from;
		}

		// check passport is already exists in courier table
		$check_cr = Courier::where('pass_no','=',$pass_no)->first();
		if(isset($check_cr)){

			// Upload in Courier Clone
			$courierC = new Courierclone();

			if(isset($cand)){
				$courierC->cand_id = $cand->cand_id;
			}else{
				$courierC->cand_id = null;
			}

			$courierC->pty_id = $pty_id;
			$courierC->pass_no = $pass_no;
			$courierC->cand_fname = $request->cand_fname;
			$courierC->cand_mname = $request->cand_mname;
			$courierC->cand_lname = $request->cand_lname;
			if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
				$courierC->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
			}else{
				$courierC->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
			}
			$courierC->care_of_id = $request->care_of_id;
			$courierC->courier_from = $request->cand_from;
			if($request->pty_id != '200'){
				$courierC->courier_to = $request->cand_to;
			}else{	
				$courierC->courier_to_add = $request->cand_to_add;
			}

			$courierC->valid_pass = $request->valid_passport;
			$courierC->old_pass = $request->old_passport;
			$courierC->driving_lic = $request->driving_license;
			$courierC->exit_paper = $request->exit_paper;
			$courierC->photo_r = $request->photo;	
			$courierC->med_rep = $request->medical_report;
			$courierC->other_doc = $request->other_documents;
			$courierC->doc_name = $request->documents_name;
			$courierC->csn_id = $request->cs_id;
			$courierC->user_id = Auth::user()->user_id;
			if($courier_mode == 0){
				$courierC->stage = 'Courier Order';
				$courierC->in_out = 'out';
			}else{
				$courierC->stage = 'Received';
				$courierC->in_out = 'in';
			}
			if($request->groupid == 'none'){
				$courierC->groupid = 'qamr'.time();
			}else{
				$courierC->groupid = $request->groupid;
			}
			$courierC->courier_mode = $courier_mode;
			$courierC->br_id = $branch;
			$courierC->save();

			// Get last ID
			$last_id = $courierC->id;

			// Update in Courier
			$courier = Courier::where('pass_no','=',$pass_no)->first();

			if(isset($cand)){
				$courier->cand_id = $cand->cand_id;
			}else{
				$courier->cand_id = null;
			}

			$courier->pty_id = $pty_id;
			$courier->care_of_id = $request->care_of_id;
			$courier->courier_from = $request->cand_from;
			if($request->pty_id != '200'){
				$courier->courier_to = $request->cand_to;
			}else{	
				$courier->courier_to_add = $request->cand_to_add;
			}

			$courier->valid_pass = $request->valid_passport;
			$courier->old_pass = $request->old_passport;
			$courier->driving_lic = $request->driving_license;
			$courier->exit_paper = $request->exit_paper;
			$courier->photo_r = $request->photo;	
			$courier->med_rep = $request->medical_report;
			$courier->other_doc = $request->other_documents;
			$courier->doc_name = $request->documents_name;
			$courier->csn_id = $request->cs_id;
			$courier->user_id = Auth::user()->user_id;
			if($courier_mode == 0){
				$courier->stage = 'Courier Order';
				$courier->in_out = 'out';
			}else{

				$courier->stage = 'Received';
				$courier->in_out = 'in';
			}
			$courier->groupid = $courierC->groupid;
			$courier->courier_mode = $courier_mode;
			$courier->last_clone_id = $last_id;
			$courier->br_id = $branch;
			$courier->save();

			// Update in Courier Details and Courier Services
			$courier_status = new CourierStatus();
			$courier_detail = new CourierDetails();

			$courier_status->courier_id = $last_id;
			$courier_status->pass_no = $pass_no;
			if($courier_mode == 0){
				$courier_status->stage = 'Courier Order';
			}else{
				$courier_status->received = '1';
				$courier_status->stage = 'Received';
			}
			$courier_status->user_id = Auth::user()->user_id;
			$courier_status->save();

			$courier_detail->courier_id = $last_id;
			$courier_detail->pass_no = $pass_no;
			if($courier_mode == 1){
				$courier_detail->csn_id = $request->cs_id;
				$courier_detail->r_valid_pass = $request->valid_passport;
				$courier_detail->r_old_pass = $request->old_passport;
				$courier_detail->r_driving_lic = $request->driving_license;
				$courier_detail->r_exit_paper = $request->exit_paper;
				$courier_detail->r_photo_r = $request->photo;	
				$courier_detail->r_med_rep = $request->medical_report;
				$courier_detail->received_by_id = Auth::user()->user_id;
				$courier_detail->receiver_name = $request->recv_name;
				$courier_detail->received_data = Carbon::now();
			}
			$courier_detail->user_id = Auth::user()->user_id;
			$courier_detail->save();

			if($courier_mode == '0'){
				Session::flash('success', 'Courier Order Add Successfully');
			}else{
				Session::flash('success', 'Courier received Successfully');
			}

		}else{
			// upload in Courier Clone
			$post = new Courierclone();
			if(isset($cand)){
				$post->cand_id = $cand->cand_id;
			}else{
				$post->cand_id = null;
			}

			$post->pty_id = $pty_id;
			$post->pass_no = $pass_no;
			$post->cand_fname = $request->cand_fname;
			$post->cand_mname = $request->cand_mname;
			$post->cand_lname = $request->cand_lname;
			if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
				$post->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
			}else{
				$post->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
			}
			$post->care_of_id = $request->care_of_id;
			$post->courier_from = $request->cand_from;
			if($request->pty_id != '200'){
				$post->courier_to = $request->cand_to;
			}else{	
				$post->courier_to_add = $request->cand_to_add;
			}

			$post->valid_pass = $request->valid_passport;
			$post->old_pass = $request->old_passport;
			$post->driving_lic = $request->driving_license;
			$post->exit_paper = $request->exit_paper;
			$post->photo_r = $request->photo;	
			$post->med_rep = $request->medical_report;
			$post->other_doc = $request->other_documents;
			$post->doc_name = $request->documents_name;
			$post->csn_id = $request->cs_id;
			$post->user_id = Auth::user()->user_id;
			if($courier_mode == 0){
				$post->stage = 'Courier Order';
				$post->in_out = 'out';
			}else{
				$post->stage = 'Received';
				$post->in_out = 'in';
			}
			if($request->groupid == 'none'){
				$post->groupid = 'qamr'.time();
			}else{
				$post->groupid = $request->groupid;
			}
			$post->courier_mode = $courier_mode;
			$post->br_id = $branch;
			$post->save();

			// Get last ID
			$last_id = $post->id;

			// Upload in Courier
			$postC = new Courier();

			if(isset($cand)){
				$postC->cand_id = $cand->cand_id;
			}else{
				$postC->cand_id = null;
			}

			$postC->pty_id = $pty_id;
			$postC->pass_no = $pass_no;
			$postC->cand_fname = $request->cand_fname;
			$postC->cand_mname = $request->cand_mname;
			$postC->cand_lname = $request->cand_lname;
			if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
				$postC->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
			}else{
				$postC->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
			}
			$postC->care_of_id = $request->care_of_id;
			$postC->courier_from = $request->cand_from;
			if($request->pty_id != '200'){
				$postC->courier_to = $request->cand_to;
			}else{	
				$postC->courier_to_add = $request->cand_to_add;
			}

			$postC->valid_pass = $request->valid_passport;
			$postC->old_pass = $request->old_passport;
			$postC->driving_lic = $request->driving_license;
			$postC->exit_paper = $request->exit_paper;
			$postC->photo_r = $request->photo;	
			$postC->med_rep = $request->medical_report;
			$postC->other_doc = $request->other_documents;
			$postC->doc_name = $request->documents_name;
			$postC->csn_id = $request->cs_id;
			$postC->user_id = Auth::user()->user_id;
			if($courier_mode == 0){
				$postC->stage = 'Courier Order';
				$postC->in_out = 'out';
			}else{
				$postC->stage = 'Received';
				$postC->in_out = 'in';
			}
			$postC->groupid = $post->groupid;
			$postC->courier_mode = $courier_mode;
			$postC->last_clone_id = $last_id;
			$postC->br_id = $branch;
			$postC->save();

			// Update in Courier Details and Courier Services
			$courier_status = new CourierStatus();
			$courier_detail = new CourierDetails();

			$courier_status->courier_id = $last_id;
			$courier_status->pass_no = $pass_no;
			if($courier_mode == 0){
				$courier_status->stage = 'Courier Order';
			}else{
				$courier_status->received = '1';
				$courier_status->stage = 'Received';
			}
			$courier_status->user_id = Auth::user()->user_id;
			$courier_status->save();			

			$courier_detail->courier_id = $last_id;
			$courier_detail->pass_no = $pass_no;
			$courier_detail->user_id = Auth::user()->user_id;
			if($courier_mode == 1){
				$courier_detail->csn_id = $request->cs_id;
				$courier_detail->r_valid_pass = $request->valid_passport;
				$courier_detail->r_old_pass = $request->old_passport;
				$courier_detail->r_driving_lic = $request->driving_license;
				$courier_detail->r_exit_paper = $request->exit_paper;
				$courier_detail->r_photo_r = $request->photo;	
				$courier_detail->r_med_rep = $request->medical_report;
				$courier_detail->received_by_id = Auth::user()->user_id;
				$courier_detail->receiver_name = $request->recv_name;
				$courier_detail->received_data = Carbon::now();
			}
			$courier_detail->save();

			if($courier_mode == '0'){
				Session::flash('success', 'Courier Order Add Successfully');
			}else{
				Session::flash('success', 'Courier received Successfully');
			}
		}

		return redirect()->back();
	}

	public function courierStore4(Request $request){
		$pass_no = $request->pass_no;
		$courier_mode = $request->courier_mode;
		// $pty_id = $request->pty_id;
		$cand = Candidate::where('cand_passport_no','=',$pass_no)->first();
		
		$groupIDC = Courierclone::select('groupid')->groupBy('groupid')->where('groupid','!=','')->get();
		
		// $countGroupid = $groupIDC->count();
		// $latesgGroup = $countGroupid + 1;
		// dd($latesgGroup);

		// dd($request->groupid);

		// Get staff brach
		if(Auth::user()->user_type != 1){
			$staff_br = DB::table('qr_staff_tbl')->where('staff_id','=',Auth::user()->user_id)->first();
			$branch = $staff_br->staff_branch;
		}else{
			if($courier_mode == 0 || $courier_mode == 2){
				$branch = $request->cand_from;
			}else{
				$branch = $request->courier_to_br_id;
			}
		}

		// check passport is already exists in courier table
		$check_cr = Courier::where('pass_no','=',$pass_no)->first();
		if(isset($check_cr)){

			// Upload in Courier Clone
			$courierC = new Courierclone();

			if(isset($cand)){
				$courierC->cand_id = $cand->cand_id;
			}else{
				$courierC->cand_id = null;
			}

			$courierC->pass_no = $pass_no;
			$courierC->cand_fname = $request->cand_fname;
			$courierC->cand_mname = $request->cand_mname;
			$courierC->cand_lname = $request->cand_lname;
			if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
				$courierC->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
			}else{
				$courierC->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
			}
			$courierC->care_of_id = $request->care_of_id;

			// insert data based on courier_mode
			if($courier_mode == 0){
				$courierC->pty_id = $request->pty_id1;
				$courierC->courier_from = $request->cand_from;
				$courierC->add_type = $request->addcr_type;
				if($request->addcr_type == 'branch'){
					$courierC->courier_to_br_id = $request->courier_to_branch_id;
				}elseif($request->addcr_type == 'party'){
					$courierC->courier_to = $request->cand_to;
					// if($request->cand_to == 16){
					// 	$courierC->courier_to_add = $request->cand_to_add;
					// 	// add new address in Party Address
					// 	// $addParty = PartyAddress::where('pty_id','=',$request->pty_id1)->first();
					// 	// $addParty->address = $request->cand_to_add;
					// 	// $addParty->save();
					// }
				}else{
					$courierC->cand_to_add_nc = $request->discandaddnc;
				}
				$courierC->stage = 'Courier Order';
				$courierC->in_out = 'out';
			}elseif($courier_mode == 1){
				$courierC->pty_id = $request->pty_id2;
				$courierC->add_type = $request->from_add_type;
				if($request->from_add_type == 'branch'){
					$courierC->courier_from_br_id =  $request->fr_br_id;
				}elseif($request->from_add_type == 'party'){
					$courierC->courier_from_pty_id = $request->courier_from_pty_id;
					// if($request->courier_from_pty_id == 16){
					// 	$courierC->courier_from_pty = $request->cour_from_none_addr;
					// }
				}else{
					$courierC->cand_fr_add_cr = $request->cour_from_none_addr;
				}
				$courierC->courier_to_br_id =  $request->courier_to_br_id;
				$courierC->stage = 'Received';
				$courierC->in_out = 'in';
			}else{
				$courierC->pty_id = $request->pty_id1;
				$courierC->courier_from = $request->cand_from;

				$courierC->stage = 'Delivered at Office';
				$courierC->in_out = 'in';
			}


			// if($courier_mode == 0 || $courier_mode == 2){
			// 	$courierC->courier_from = $request->cand_from;
			// 	$courierC->pty_id = $request->pty_id1;
			// 	if($request->pty_id1 != '200'){
			// 		$courierC->courier_to = $request->cand_to;
			// 	}else{	
			// 		$courierC->courier_to_add = $request->cand_to_add;
			// 	}
			// 	$courierC->courier_to_br_id = $request->courier_to_branch_id;
			// }else{
			// 	$courierC->pty_id = $request->pty_id2;

			// 	if($request->pty_id2 != '200'){
			// 		$courierC->courier_from_pty_id = $request->courier_from_pty_id;
			// 	}else{
			// 		$courierC->courier_from_pty = $request->courier_from_pty;
			// 	}

			// 	$courierC->courier_to_br_id =  $request->courier_to_br_id;
			// }
			
			$courierC->valid_pass = $request->valid_passport;
			$courierC->old_pass = $request->old_passport;
			$courierC->driving_lic = $request->driving_license;
			$courierC->exit_paper = $request->exit_paper;
			$courierC->photo_r = $request->photo;	
			$courierC->med_rep = $request->medical_report;
			$courierC->other_doc = $request->other_documents;
			$courierC->doc_name = $request->documents_name;
			$courierC->medical_token = $request->medical_token;
			// if($courier_mode != 2){
			// 	$courierC->csn_id = $request->cs_id;
			// }

			$courierC->user_id = Auth::user()->user_id;
			// if($courier_mode == 0){
			// 	$courierC->stage = 'Courier Order';
			// 	$courierC->in_out = 'out';
			// }elseif($courier_mode == 1){
			// 	$courierC->stage = 'Received';
			// 	$courierC->in_out = 'in';
			// }else{
			// 	$courierC->stage = 'Delivered at Office';
			// 	$courierC->in_out = 'in';
			// }

			// if($request->groupid == 'none'){
			// 	$courierC->groupid = $latesgGroup;
			// }else{
			// 	$courierC->groupid = $request->groupid;
			// }
			$courierC->courier_mode = $courier_mode;
			$courierC->br_id = $branch;
			$courierC->courier_entry_data = Carbon::now();
			$courierC->rec_contact = $request->rec_contact;
			$courierC->rec_name_dao = $request->rec_name_dao;
			$courierC->rec_cont_dao = $request->rec_cont_dao;
			$courierC->c_person = $request->c_person;
			$courierC->save();

			// Get last ID
			$last_id = $courierC->id;
			$courier_entry_data = $courierC->courier_entry_data;

			// Update in Courier
			$courier = Courier::where('pass_no','=',$pass_no)->first();

			if(isset($cand)){
				$courier->cand_id = $cand->cand_id;
			}else{
				$courier->cand_id = null;
			}

			if($courier_mode == 0){
				$courier->pty_id = $request->pty_id1;
				$courier->courier_from = $request->cand_from;
				$courier->add_type = $request->addcr_type;
				if($request->addcr_type == 'branch'){
					$courier->courier_to_br_id = $request->courier_to_branch_id;
				}elseif($request->addcr_type == 'party'){
					$courier->courier_to = $request->cand_to;
					// if($request->cand_to == 16){
					// 	$courier->courier_to_add = $request->cand_to_add;
					// }
				}else{
					$courier->cand_to_add_nc = $request->discandaddnc;
				}
				$courier->stage = 'Courier Order';
				$courier->in_out = 'out';
			}elseif($courier_mode == 1){
				$courier->pty_id = $request->pty_id2;
				$courier->add_type = $request->from_add_type;
				if($request->from_add_type == 'branch'){
					$courier->courier_from_br_id =  $request->fr_br_id;
				}elseif($request->from_add_type == 'party'){
					$courier->courier_from_pty_id = $request->courier_from_pty_id;
					// if($request->courier_from_pty_id == 16){
					// 	$courier->courier_from_pty = $request->cour_from_none_addr;
					// }
				}else{
					$courier->cand_fr_add_cr = $request->cour_from_none_addr;
				}
				$courier->courier_to_br_id =  $request->courier_to_br_id;
				$courier->stage = 'Received';
				$courier->in_out = 'in';
			}else{
				$courier->pty_id = $request->pty_id1;
				$courier->courier_from = $request->cand_from;

				$courier->stage = 'Delivered at Office';
				$courier->in_out = 'in';
			}


			$courier->care_of_id = $request->care_of_id;
			// if($courier_mode == 0 || $courier_mode == 2){
			// 	$courier->courier_from = $request->cand_from;
			// 	$courier->pty_id = $request->pty_id1;
			// 	if($request->pty_id1 != '200'){
			// 		$courier->courier_to = $request->cand_to;
			// 	}else{	
			// 		$courier->courier_to_add = $request->cand_to_add;
			// 	}

			// 	$courier->courier_to_br_id = $request->courier_to_branch_id;
			// }else{
			// 	$courier->pty_id = $request->pty_id2;
			// 	$courier->courier_from_pty = $request->courier_from_pty;
			// 	$courier->courier_to_br_id =  $request->courier_to_br_id;
			// }

			// $courier->courier_from = $request->cand_from;
			// if($request->pty_id != '200'){
			// 	$courier->courier_to = $request->cand_to;
			// }else{	
			// 	$courier->courier_to_add = $request->cand_to_add;
			// }

			$courier->valid_pass = $request->valid_passport;
			$courier->old_pass = $request->old_passport;
			$courier->driving_lic = $request->driving_license;
			$courier->exit_paper = $request->exit_paper;
			$courier->photo_r = $request->photo;	
			$courier->med_rep = $request->medical_report;
			$courier->other_doc = $request->other_documents;
			$courier->doc_name = $request->documents_name;
			$courier->medical_token = $request->medical_token;
			// if($courier_mode != 2){
			// 	$courier->csn_id = $request->cs_id;
			// }
			$courier->user_id = Auth::user()->user_id;
			// if($courier_mode == 0){
			// 	$courier->stage = 'Courier Order';
			// 	$courier->in_out = 'out';
			// }elseif($courier_mode == 1){
			// 	$courier->stage = 'Received';
			// 	$courier->in_out = 'in';
			// }else{
			// 	$courier->stage = 'Delivered at Office';
			// 	$courier->in_out = 'out';
			// }
			// $courier->groupid = $courierC->groupid;
			$courier->courier_mode = $courier_mode;
			$courier->last_clone_id = $last_id;
			$courier->br_id = $branch;
			$courier->courier_entry_data = $courier_entry_data;
			$courier->rec_contact = $request->rec_contact;
			$courier->rec_name_dao = $request->rec_name_dao;
			$courier->rec_cont_dao = $request->rec_cont_dao;
			$courier->c_person = $request->c_person;
			$courier->save();

			// Update in Courier Details and Courier Services
			$courier_status = new CourierStatus();
			$courier_detail = new CourierDetails();

			$courier_status->courier_id = $last_id;
			$courier_status->pass_no = $pass_no;
			if($courier_mode == 0){
				$courier_status->stage = 'Courier Order';
			}elseif($courier_mode == 1){
				$courier_status->received = '1';
				$courier_status->stage = 'Received';
			}else{
				$courier_status->delivered_at_office = '1';
				$courier_status->stage = 'Delivered at Office';
			}
			$courier_status->user_id = Auth::user()->user_id;
			$courier_status->save();

			$courier_detail->courier_id = $last_id;
			$courier_detail->pass_no = $pass_no;
			if($courier_mode == 1){
				// $courier_detail->csn_id = $request->cs_id;
				$courier_detail->r_valid_pass = $request->valid_passport;
				$courier_detail->r_old_pass = $request->old_passport;
				$courier_detail->r_driving_lic = $request->driving_license;
				$courier_detail->r_exit_paper = $request->exit_paper;
				$courier_detail->r_photo_r = $request->photo;	
				$courier_detail->r_med_rep = $request->medical_report;
				$courier_detail->r_medical_token = $request->medical_token;
				$courier_detail->received_by_id = Auth::user()->user_id;
				// $courier_detail->receiver_name = $request->recv_name;
				$courier_detail->receiver_name = Auth::user()->name;
				$courier_detail->received_data = Carbon::now();
			}

			if($courier_mode == 2){
				$courier_detail->valid_pass = $request->valid_passport;
				$courier_detail->old_pass = $request->old_passport;
				$courier_detail->driving_lic = $request->driving_license;
				$courier_detail->exit_paper = $request->exit_paper;
				$courier_detail->photo_r = $request->photo;	
				$courier_detail->med_rep = $request->medical_report;
				$courier_detail->medical_token = $request->medical_token;
				$courier_detail->delivered_atoffice_id = Auth::user()->user_id;
			}

			$courier_detail->user_id = Auth::user()->user_id;
			$courier_detail->save();


			// Upload Courier Notification List if stage is receied
			if($courier_mode == 1){
				$uploadCN = new CourerNotificationList();
				$uploadCN->candidate_name = $courierC->cand_fullname;
				$uploadCN->pass_no = $courierC->pass_no;
				$uploadCN->pty_id = $courierC->pty_id;
				$uploadCN->care_of_id = $courierC->care_of_id;
				$uploadCN->dor = $courierC->created_at;
				$uploadCN->stage = $courierC->stage;
				$uploadCN->valid_pass = $courierC->valid_pass;
				$uploadCN->old_pass = $courierC->old_pass;
				$uploadCN->driving_lic = $courierC->driving_lic;
				$uploadCN->exit_paper = $courierC->exit_paper;
				$uploadCN->photo_r = $courierC->photo_r;
				$uploadCN->med_rep = $courierC->med_rep;
				$uploadCN->other_doc = $courierC->other_doc;
				$uploadCN->doc_name = $courierC->doc_name;
				// $uploadCN->csn_id = $courierC->csn_id;
				$uploadCN->medical_token = $courierC->medical_token;
				$uploadCN->save();

				// Party Details
				$party = Party::where('pty_id','=',$courierC->pty_id)->first();
				$user = User::where('user_id','=',$courierC->care_of_id)->first();
				$branch25 = Branch::where('br_id','=',$request->courier_to_br_id)->first();
				// $csn = CourierServiceName::where('id','=',$courierC->csn_id)->first();
				// CourierNotificationM::dispatch($uploadCN,$party,$user,$csn)->onQueue('courier');
				// CourierNotificationW::dispatch($uploadCN,$party,$user,$csn)->onQueue('courier');
				CourierNotificationM::dispatch($uploadCN,$party,$user,$branch25)->onQueue('courier');
				CourierNotificationW::dispatch($uploadCN,$party,$user,$branch25)->onQueue('courier');
			}
			
			// Uplload Courier Notificarion List if Stage is Courier Order
			if($courier_mode == 0){
				$uploadCN = new CourerNotificationList();
				$uploadCN->candidate_name = $courierC->cand_fullname;
				$uploadCN->pass_no = $courierC->pass_no;
				$uploadCN->pty_id = $courierC->pty_id;
				$uploadCN->care_of_id = $courierC->care_of_id;
				$uploadCN->save();

				// Party Details
				$party = Party::where('pty_id','=',$courierC->pty_id)->first();
				$user = User::where('user_id','=',$courierC->care_of_id)->first();

				CourierNotificationRRM::dispatch($uploadCN,$party,$user)->onQueue('courier');
				CourierNotificationRRW::dispatch($uploadCN,$party,$user)->onQueue('courier');

			}

			// Send Notificatio if Courier mode is deliver at office
			if($courier_mode == 2){
				// Get API
				$getPrty = Party::where('pty_id','=',$courierC->pty_id)->first();
				$getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();
				CourierDeliveratOffce::dispatch($courierC,$getAPI,$getPrty)->onQueue('default');
			}

			if($courier_mode == '0'){
				Session::flash('success', 'Courier Order Add Successfully');
			}elseif($courier_mode == '1'){
				Session::flash('success', 'Courier received Successfully');
			}else{
				Session::flash('success', 'Courier delivered at office Successfully');
			}

		}else{
			// upload in Courier Clone
			$post = new Courierclone();
			if(isset($cand)){
				$post->cand_id = $cand->cand_id;
			}else{
				$post->cand_id = null;
			}

			$post->pass_no = $pass_no;
			$post->cand_fname = $request->cand_fname;
			$post->cand_mname = $request->cand_mname;
			$post->cand_lname = $request->cand_lname;
			if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
				$post->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
			}else{
				$post->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
			}
			$post->care_of_id = $request->care_of_id;

			// insert data based on courier_mode
			if($courier_mode == 0){
				$post->pty_id = $request->pty_id1;
				$post->courier_from = $request->cand_from;
				$post->add_type = $request->addcr_type;
				if($request->addcr_type == 'branch'){
					$post->courier_to_br_id = $request->courier_to_branch_id;
				}elseif($request->addcr_type == 'party'){
					$post->courier_to = $request->cand_to;
					// if($request->cand_to == 16){
					// 	$post->courier_to_add = $request->cand_to_add;
					// 	// add new address in Party Address
					// 	// $addParty = PartyAddress::where('pty_id','=',$request->pty_id1)->first();
					// 	// $addParty->address = $request->cand_to_add;
					// 	// $addParty->save();
					// }
				}else{
					$post->cand_to_add_nc = $request->discandaddnc;
				}
				$post->stage = 'Courier Order';
				$post->in_out = 'out';
			}elseif($courier_mode == 1){
				$post->pty_id = $request->pty_id2;
				$post->add_type = $request->from_add_type;
				if($request->from_add_type == 'branch'){
					$post->courier_from_br_id =  $request->fr_br_id;
				}elseif($request->from_add_type == 'party'){
					$post->courier_from_pty_id = $request->courier_from_pty_id;
					// if($request->courier_from_pty_id == 16){
					// 	$post->courier_from_pty = $request->cour_from_none_addr;
					// }
				}else{
					$post->cand_fr_add_cr = $request->cour_from_none_addr;
				}
				$post->courier_to_br_id =  $request->courier_to_br_id;
				$post->stage = 'Received';
				$post->in_out = 'in';
			}else{
				$post->pty_id = $request->pty_id1;
				$post->courier_from = $request->cand_from;

				$post->stage = 'Delivered at Office';
				$post->in_out = 'in';
			}

			// if($courier_mode == 0 || $courier_mode == 2){
			// 	$post->courier_from = $request->cand_from;
			// 	$post->pty_id = $request->pty_id1;
			// 	if($request->pty_id1 != '200'){
			// 		$post->courier_to = $request->cand_to;
			// 	}else{	
			// 		$post->courier_to_add = $request->cand_to_add;
			// 	}
			// 	$post->courier_to_br_id = $request->courier_to_branch_id;
			// }else{
			// 	$post->pty_id = $request->pty_id2;
			// 	if($request->pty_id2 != '200'){
			// 		$post->courier_from_pty_id = $request->courier_from_pty_id;

			// 		if($request->courier_from_pty_id == 16){
			// 			$post->party_address = $request->cour_from_none_addr;
			// 		}

			// 	}else{
			// 		$post->courier_from_pty = $request->courier_from_pty;
			// 	}

				
			// 	$post->courier_to_br_id =  $request->courier_to_br_id;
			// }

			$post->valid_pass = $request->valid_passport;
			$post->old_pass = $request->old_passport;
			$post->driving_lic = $request->driving_license;
			$post->exit_paper = $request->exit_paper;
			$post->photo_r = $request->photo;	
			$post->med_rep = $request->medical_report;
			$post->other_doc = $request->other_documents;
			$post->medical_token = $request->medical_token;
			$post->doc_name = $request->documents_name;
			// if($courier_mode != 2){
			// 	$post->csn_id = $request->cs_id;
			// }
			$post->user_id = Auth::user()->user_id;
			// if($courier_mode == 0){
			// 	$post->stage = 'Courier Order';
			// 	$post->in_out = 'out';
			// }elseif($courier_mode == 1){
			// 	$post->stage = 'Received';
			// 	$post->in_out = 'in';
			// }else{
			// 	$post->stage = 'Delivered at Office';
			// 	$post->in_out = 'out';
			// }

			// if($request->groupid == 'none'){
			// 	$post->groupid = $latesgGroup;
			// }else{
			// 	$post->groupid = $request->groupid;
			// }

			$post->courier_mode = $courier_mode;
			$post->br_id = $branch;
			$post->courier_entry_data = Carbon::now();
			$post->rec_contact = $request->rec_contact;
			$post->rec_name_dao = $request->rec_name_dao;
			$post->rec_cont_dao = $request->rec_cont_dao;
			$post->c_person = $request->c_person;
			$post->save();

			// Get last ID
			$last_id = $post->id;
			$courier_entry_data = $post->courier_entry_data;
			// Upload in Courier
			$postC = new Courier();

			if(isset($cand)){
				$postC->cand_id = $cand->cand_id;
			}else{
				$postC->cand_id = null;
			}

			$postC->pass_no = $pass_no;
			$postC->cand_fname = $request->cand_fname;
			$postC->cand_mname = $request->cand_mname;
			$postC->cand_lname = $request->cand_lname;
			if($request->cand_fname !='' && $request->cand_mname !='' && $request->cand_lname !=''){
				$postC->cand_fullname = $request->cand_fname.' '.$request->cand_mname.' '.$request->cand_lname;
			}else{
				$postC->cand_fullname = $request->cand_fname.' '.$request->cand_lname;
			}
			$postC->care_of_id = $request->care_of_id;

			// insert data based on courier_mode
			if($courier_mode == 0){
				$postC->pty_id = $request->pty_id1;
				$postC->courier_from = $request->cand_from;
				$postC->add_type = $request->addcr_type;
				if($request->addcr_type == 'branch'){
					$postC->courier_to_br_id = $request->courier_to_branch_id;
				}elseif($request->addcr_type == 'party'){
					$postC->courier_to = $request->cand_to;
					// if($request->cand_to == 16){
					// 	$postC->courier_to_add = $request->cand_to_add;
					// }
				}else{
					$postC->cand_to_add_nc = $request->discandaddnc;
				}
				$postC->stage = 'Courier Order';
				$postC->in_out = 'out';
			}elseif($courier_mode == 1){
				$postC->pty_id = $request->pty_id2;
				$postC->add_type = $request->from_add_type;
				if($request->from_add_type == 'branch'){
					$postC->courier_from_br_id =  $request->fr_br_id;
				}elseif($request->from_add_type == 'party'){
					$postC->courier_from_pty_id = $request->courier_from_pty_id;
					// if($request->courier_from_pty_id == 16){
					// 	$postC->courier_from_pty = $request->cour_from_none_addr;
					// }
				}else{
					$postC->cand_fr_add_cr = $request->cour_from_none_addr;
				}
				$postC->courier_to_br_id =  $request->courier_to_br_id;
				$postC->stage = 'Received';
				$postC->in_out = 'in';
			}else{
				$postC->pty_id = $request->pty_id1;
				$postC->courier_from = $request->cand_from;

				$postC->stage = 'Delivered at Office';
				$postC->in_out = 'in';
			}

			// if($courier_mode == 0 || $courier_mode == 2){

			// 	$postC->courier_from = $request->cand_from;
			// 	$postC->pty_id = $request->pty_id1;
			// 	if($request->pty_id1 != '200'){
			// 		$postC->courier_to = $request->cand_to;
			// 	}else{	
			// 		$postC->courier_to_add = $request->cand_to_add;
			// 	}

			// 	$postC->courier_to_br_id = $request->courier_to_branch_id;
			// }else{
			// 	$postC->pty_id = $request->pty_id2;
			// 	$postC->courier_from_pty = $request->courier_from_pty;
			// 	$postC->courier_to_br_id =  $request->courier_to_br_id;
			// }

			$postC->valid_pass = $request->valid_passport;
			$postC->old_pass = $request->old_passport;
			$postC->driving_lic = $request->driving_license;
			$postC->exit_paper = $request->exit_paper;
			$postC->photo_r = $request->photo;	
			$postC->med_rep = $request->medical_report;
			$postC->other_doc = $request->other_documents;
			$postC->doc_name = $request->documents_name;
			$postC->medical_token = $request->medical_token;
			// if($courier_mode != 2){
			// 	$postC->csn_id = $request->cs_id;
			// }
			$postC->user_id = Auth::user()->user_id;
			// if($courier_mode == 0){
			// 	$postC->stage = 'Courier Order';
			// 	$postC->in_out = 'out';
			// }elseif($courier_mode == 1){
			// 	$postC->stage = 'Received';
			// 	$postC->in_out = 'in';
			// }else{
			// 	$postC->stage = 'Delivered at Office';
			// 	$postC->in_out = 'out';
			// }
			// $postC->groupid = $post->groupid;
			$postC->courier_mode = $courier_mode;
			$postC->last_clone_id = $last_id;
			$postC->br_id = $branch;
			$postC->courier_entry_data = $courier_entry_data;
			$postC->rec_contact = $request->rec_contact;
			$postC->rec_name_dao = $request->rec_name_dao;
			$postC->rec_cont_dao = $request->rec_cont_dao;
			$postC->c_person = $request->c_person;
			$postC->save();

			// Update in Courier Details and Courier Services
			$courier_status = new CourierStatus();
			$courier_detail = new CourierDetails();

			$courier_status->courier_id = $last_id;
			$courier_status->pass_no = $pass_no;
			if($courier_mode == 0){
				$courier_status->stage = 'Courier Order';
			}elseif($courier_mode == 1){
				$courier_status->received = '1';
				$courier_status->stage = 'Received';
			}else{
				$courier_status->delivered_at_office = '1';
				$courier_status->stage = 'Delivered at Office';
			}
			$courier_status->user_id = Auth::user()->user_id;
			$courier_status->save();			

			$courier_detail->courier_id = $last_id;
			$courier_detail->pass_no = $pass_no;
			$courier_detail->user_id = Auth::user()->user_id;
			if($courier_mode == 1){
				// $courier_detail->csn_id = $request->cs_id;
				$courier_detail->r_valid_pass = $request->valid_passport;
				$courier_detail->r_old_pass = $request->old_passport;
				$courier_detail->r_driving_lic = $request->driving_license;
				$courier_detail->r_exit_paper = $request->exit_paper;
				$courier_detail->r_photo_r = $request->photo;	
				$courier_detail->r_med_rep = $request->medical_report;
				$courier_detail->r_medical_token = $request->medical_token;
				$courier_detail->received_by_id = Auth::user()->user_id;
				// $courier_detail->receiver_name = $request->recv_name;
				$courier_detail->receiver_name = Auth::user()->name;
				$courier_detail->received_data = Carbon::now();
			}

			if($courier_mode == 2){
				$courier_detail->valid_pass = $request->valid_passport;
				$courier_detail->old_pass = $request->old_passport;
				$courier_detail->driving_lic = $request->driving_license;
				$courier_detail->exit_paper = $request->exit_paper;
				$courier_detail->photo_r = $request->photo;	
				$courier_detail->med_rep = $request->medical_report;
				$courier_detail->medical_token = $request->medical_token;
				$courier_detail->delivered_atoffice_id = Auth::user()->user_id;
			}

			$courier_detail->save();


			// Upload Courier Notification List if stage is receied
			if($courier_mode == 1){
				$uploadCN = new CourerNotificationList();
				$uploadCN->candidate_name = $post->cand_fullname;
				$uploadCN->pass_no = $post->pass_no;
				$uploadCN->pty_id = $post->pty_id;
				$uploadCN->care_of_id = $post->care_of_id;
				$uploadCN->dor = $post->created_at;
				$uploadCN->stage = $post->stage;
				$uploadCN->valid_pass = $post->valid_pass;
				$uploadCN->old_pass = $post->old_pass;
				$uploadCN->driving_lic = $post->driving_lic;
				$uploadCN->exit_paper = $post->exit_paper;
				$uploadCN->photo_r = $post->photo_r;
				$uploadCN->med_rep = $post->med_rep;
				$uploadCN->other_doc = $post->other_doc;
				$uploadCN->doc_name = $post->doc_name;
				// $uploadCN->csn_id = $post->csn_id;
				$uploadCN->medical_token = $post->medical_token;
				$uploadCN->save();

				// Party Details
				$party = Party::where('pty_id','=',$post->pty_id)->first();
				$user = User::where('user_id','=',$post->care_of_id)->first();
				$branch25 = Branch::where('br_id','=',$request->courier_to_br_id)->first();
				// $csn = CourierServiceName::where('id','=',$post->csn_id)->first();
				// CourierNotificationM::dispatch($uploadCN,$party,$user,$csn)->onQueue('courier');
				// CourierNotificationW::dispatch($uploadCN,$party,$user,$csn)->onQueue('courier');
				CourierNotificationM::dispatch($uploadCN,$party,$user,$branch25)->onQueue('courier');
				CourierNotificationW::dispatch($uploadCN,$party,$user,$branch25)->onQueue('courier');
			}

			// Uplload Courier Notificarion List if Stage is Courier Order
			if($courier_mode == 0){
				$uploadCN = new CourerNotificationList();
				$uploadCN->candidate_name = $post->cand_fullname;
				$uploadCN->pass_no = $post->pass_no;
				$uploadCN->pty_id = $post->pty_id;
				$uploadCN->care_of_id = $post->care_of_id;
				$uploadCN->save();

				// Party Details
				$party = Party::where('pty_id','=',$post->pty_id)->first();
				$user = User::where('user_id','=',$post->care_of_id)->first();

				CourierNotificationRRM::dispatch($uploadCN,$party,$user)->onQueue('courier');
				CourierNotificationRRW::dispatch($uploadCN,$party,$user)->onQueue('courier');

			}

			// Send Notificatio if Courier mode is deliver at office
			if($courier_mode == 2){
				// Get API
				$courierC = $post;
				$getPrty = Party::where('pty_id','=',$post->pty_id)->first();
				$getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();
				CourierDeliveratOffce::dispatch($courierC,$getAPI,$getPrty)->onQueue('default');
			}


			if($courier_mode == '0'){
				Session::flash('success', 'Courier Order Add Successfully');
			}elseif($courier_mode == '1'){
				Session::flash('success', 'Courier received Successfully');
			}else{
				Session::flash('success', 'Courier deliver at office Successfully');
			}
		}


	

		return redirect()->back();
	}

	public function getCandTo(Request $request){
		$pty_id = $request->input('pty_id');
		$getPadds = PartyAddress::where('pty_id','=',$pty_id)->where('pty_id','!=','200')->get();
		$res = '';
		$res .= '<option value="">Select</option><option value="16">--None--</option>';
		if(isset($getPadds)){
			foreach ($getPadds as $getPadd) {
				$res .= '<option value="'.$getPadd->id.'">'.$getPadd->address.'</option>';
			}
		}

		$data['res'] = $res;
		return response()->json($data);
	}

	public function getCandFrom(Request $request){
		$pty_id = $request->input('pty_id');
		$getPaddsa = PartyAddress::where('pty_id','=',$pty_id)->where('pty_id','!=','200')->get();
		$res = '';
		$res .= '<option value="">Select</option><option value="16">--None--</option>';
		if(isset($getPaddsa)){
			foreach ($getPaddsa as $getPadda) {
				$res .= '<option value="'.$getPadda->id.'">'.$getPadda->address.'</option>';
			}
		}
		$data['res'] = $res;
		return response()->json($data);
	}

	public function getCandFromOt(Request $request)
	{
		$pty_id = $request->input('pty_id');
		$getPaddsa = PartyAddress::where('pty_id','=',$pty_id)->where('pty_id','!=','200')->get();
		$res = '';
		$res .= '<option value="">Select</option><option value="16">--None--</option>';
		if(isset($getPaddsa)){
			foreach ($getPaddsa as $getPadda) {
				$res .= '<option value="'.$getPadda->id.'">'.$getPadda->address.'</option>';
			}
		}
		$data['res'] = $res;
		return response()->json($data);
	}

	public function getGroupID(Request $request){
		$gparty = $request->gparty;
		$gfrom = $request->gfrom;
		$gto = $request->gto;

		$getGids = Courier::where('pty_id','=',$gparty)->where('courier_from','=',$gfrom)->where('courier_to','=',$gto)->where('courier_type','=','group')->get();
		$res = '';
		$res .= '<option value = "">Select</option><option value="none">None</option>';
		if(isset($getGids)){
			foreach($getGids as $getGid){
				$res .= '<option value="'.$getGid->groupid.'">'.$getGid->groupid.' ('.$getGid->cand_fullname.'-'.$getGid->pass_no.')</option>';
			}
		}

		$data['res'] = $res;
		return response()->json($data);

	}

	public function relGroupID(Request $request){
		extract($_POST);
		$post = Courier::where('id','=',$id)->first();
		$post->groupid = '';
		$post->save();
	}

	public function cupaddr(Request $request){
		$id = $request->input('id');
		$courier_to = $request->input('val');

		// Update in Courier SD
		$cour_up = CourierDetails::where('courier_id','=',$id)->first();
		$cour_up->partyadd_id = $courier_to;
		$cour_up->save();

		// Update in Courier and courier clone
		$courier = Courier::where('last_clone_id','=',$id)->first();
		$courier->courier_to = $courier_to;
		$courier->save();

		$courierC = Courierclone::where('id','=',$id)->first();
		$courierC->courier_to = $courier_to;
		$courierC->save();

	}

	public function cupbraddr(Request $request){
		$id = $request->input('id');
		$courier_to_br = $request->input('val');


		// Update in Courier and Courier Clone
		$courier = Courier::where('last_clone_id','=',$id)->first();
		$courier->courier_to_br_id = $courier_to_br;
		$courier->save();

		$courierC = Courierclone::where('id','=',$id)->first();
		$courierC->courier_to_br_id = $courier_to_br;
		$courierC->save();
	}

	public function prtygetAdd(Request $request){
		$id = $request->val;
		if($id != 'New'){
			$post = PartyAddress::find($id);
			return response()->json($post);
		}

	}

	public function updateProfile(Request $request){
		$id = $request->input('id');
		$cour_up = Courier::find($id);
		if($request->hasFile('profile_pic')) {
    		$file = $request->file('profile_pic');
    		$file_count = File::files(base_path().'/public/image/service-candidate');
    		$filecount = 0;
			if ($file_count !== false) {
      			$filecount = count($file_count);
    		}
    		$file_exe = $file->getClientOriginalExtension();
    		$name = $filecount . '.' . $file_exe;

    		$file->move(base_path().'/public/image/service-candidate', $name);
    		$profile_pic = $name;

	  	} else {
    		$profile_pic = '';
  		}
		
		$cour_up->image = $profile_pic;
		$cour_up->update_by_id = Auth::user()->user_id;
		$cour_up->save();
		Session::flash('success','Profile Pic Updated Successfully!');
		return redirect()->back();
	}

	public function updaddCourier(Request $request){

		$courier_to_add = $request->address.','.$request->city.'-'.$request->pincode;

		$courier = Courier::find($request->courier_id);
		$courier->courier_given_name = $request->name;
		$courier->courier_given_add = $courier_to_add;
		$courier->save();
		Session::flash('success','Address updated!');
		return redirect()->back();
	}

	public function courierStage($id){
		// Get Courier Info
		$courier_info = Courier::find($id);
		// Get Candidate Details if available
		$cand_info = Candidate::where('cand_id','=',$courier_info->cand_id)->first();
		// Get Details from Service Details
		$ser_d = ServiceDetails::where('cand_id','=',$courier_info->cand_id)->first();
		// Get Courier Status
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();
		// Get Visa Details
		$EmpCand = EmployeeCandidate::where('cand_id','=',$courier_info->cand_id)->first();
		// Visa Details
		if(isset($EmpCand)){
			$visa_d = Employee::where('emp_id','=',$EmpCand->emp_id)->first();
		}else{
			$visa_d = '';
		}
		// Get Courier Details
		$courier_det = CourierDetails::where('courier_id','=',$id)->first();
		// Get Mofa No
		if(isset($ser_d) && $ser_d->mofa !=''){
			$mofa_d = unserialize($ser_d->mofa);
			$mofa_no = $mofa_d['mofa_no'];
		}else{
			$mofa_d = '';
			$mofa_no = '';
		}

		// Get group candidate
		$gcands = Courier::where('groupid','=',$courier_info->groupid)->where('groupid','!=','')->where('id','!=',$id)->get();
		// dd($mofa_no);

		// Courier List
		$courier_lists = Courier::where('groupid','!=','')->where('group_status','=',0)->groupBy('groupid')->get();

		// Branch wise view
		if(Auth::user()->user_type != 1){
			$staff = Staff::where('staff_id','=',Auth::user()->user_id)->first();
			$brStaff = $staff->staff_branch;
		}else{
			$brStaff = $courier_info->br_id;
		}

		if($brStaff == $courier_info->br_id){
			if($courier_st->courier_order == 0 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.courier-order',['pageConfigs' => $pageConfigs,'courier_info' => $courier_info,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands,'courier_lists' => $courier_lists]);
			}
			
			if($courier_st->dispatched == 0 && $courier_st->courier_order == 1 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.dispatched',['pageConfigs' => $pageConfigs,'courier_info' => $courier_info,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}
	
			if($courier_st->ongoing == 0 && $courier_st->dispatched == 1 && $courier_st->courier_order == 1 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.ongoing',['pageConfigs' => $pageConfigs,'courier_info' => $courier_info,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}
	
			if($courier_st->delivered == 0 && $courier_st->ongoing == 1 && $courier_st->dispatched == 1 && $courier_st->courier_order == 1 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.delivered',['pageConfigs' => $pageConfigs,'courier_info' => $courier_info,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}
		}else{
			if($courier_st->received == 0 && $courier_st->dispatched == 1 && $courier_st->courier_order == 1 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.received',['pageConfigs' => $pageConfigs,'courier_info' => $courier_info,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}
			
			if($courier_st->received == 1 && $courier_st->dispatched == 1 && $courier_st->courier_order == 1 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.received_final',['pageConfigs' => $pageConfigs,'courier_info' => $courier_info,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}

		}


	
		
	}

	public function uporder(Request $request){
		$id = $request->input('id');
		
		// Update in Courier
		$courier = Courier::find($id);
		$courier->csn_id = $request->input('csn_id');
		if($courier->groupid != $request->groupid){
			$courier->groupid = $request->groupid;
		}elseif($request->groupid == 'none'){
			$courier->groupid = 'qamr'.time();
		}
		
		$courier->save();

		// Update in Courier Status
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();
		if($request->input('valid_pass') == 1 || $request->input('old_pass') == 1 || $request->input('driving_lic') == 1 || $request->input('exit_paper') == 1 || $request->input('photo_r') == 1 || $request->input('med_rep') == 1){
			$courier_st->courier_order = 1;
			$courier_st->stage = 'Dispatched';
			$courier_st->save();
		}


		// update in Courier Service Details
		$courier_sd = CourierDetails::where('courier_id','=',$id)->first();
		$courier_sd->csn_id = $request->input('csn_id') ? $request->input('csn_id') :'';
		$courier_sd->valid_pass = $request->input('valid_pass') ? $request->input('valid_pass') :'';
		$courier_sd->old_pass = $request->input('old_pass') ? $request->input('old_pass') :'';
		$courier_sd->driving_lic = $request->input('driving_lic') ? $request->input('driving_lic'):''; 
		$courier_sd->exit_paper = $request->input('exit_paper') ? $request->input('exit_paper'):'';
		$courier_sd->photo_r = $request->input('photo_r') ? $request->input('photo_r'):'';
		$courier_sd->med_rep = $request->input('med_rep') ? $request->input('med_rep'):'';
		$courier_sd->dispatch_by_id = Auth::user()->user_id;
		$courier_sd->dispatch_date = Carbon::now();
		$courier_sd->partyadd_id = $request->courier_to;
		$courier_sd->groupid = $request->input('groupid');
		$courier_sd->save();

		Session::flash('success','Courier Order Details Updated!');
		return redirect('services/courier/stage-update/'.$id);

	}

	public function updispatch(Request $request){
		$id = $request->input('id');

		if($request->hasFile('courier_slip')) {
    		$file = $request->file('courier_slip');
    		$file_count = File::files(base_path().'/public/image/service-candidate');
    		$filecount = 0;
			if ($file_count !== false) {
      			$filecount = count($file_count);
    		}
    		$file_exe = $file->getClientOriginalExtension();
    		$name = $filecount . '.' . $file_exe;

    		$file->move(base_path().'/public/image/service-candidate', $name);
    		$profile_pic = $name;

	  	} else {
    		$profile_pic = '';
  		}

		

		// Update in Courier
		$courier = Courier::find($id);

		// check group exists or not
		$grCheck = Courier::where('groupid','=',$courier->groupid)->where('groupid','!=','')->get();

		if($grCheck->count() > 0){
			foreach($grCheck as $grChec){
				// Update in Courier Status
				$courier_st = CourierStatus::where('courier_id','=',$grChec->id)->first();

				// update in Courier Service Details
				$courier_sd = CourierDetails::where('courier_id','=',$grChec->id)->first();
				$courier_sd->tracking_id = $request->input('tracking_id');
				$courier_sd->courier_slip = $profile_pic;
				$courier_sd->save();

				$courier_st->dispatched = 1;
				$courier_st->stage = 'Ongoing';
				$courier_st->save();

				// Update group status in courier table
				$courierUP = Courier::find($grChec->id);
				$courierUP->group_status = 1;
				$courierUP->save();
			}
		}else{
			// Update in Courier Status
			$courier_st = CourierStatus::where('courier_id','=',$id)->first();

			// update in Courier Service Details
			$courier_sd = CourierDetails::where('courier_id','=',$id)->first();
			$courier_sd->tracking_id = $request->input('tracking_id');
			$courier_sd->courier_slip = $profile_pic;
			$courier_sd->save();

			$courier_st->dispatched = 1;
			$courier_st->stage = 'Ongoing';
			$courier_st->save();
		}

		// Add Courier Notification
		$courier_not = new Couriernotification();
		$courier_not->courier_id = $id;
		$courier_not->pty_id = $courier->pty_id;
		$courier_not->careoff_id = $courier->care_of_id;
		$courier_not->csn_id = $courier->csn_id;
		$courier_not->tracking_id = $request->input('tracking_id');
		$courier_not->courier_slip = $profile_pic;
		$courier_not->stage_name = 'Ongoing';
		$courier_not->upload_date = Carbon::now();
		$courier_not->save();

		Session::flash('success','Courier Dispatched Successfully!');
		return redirect('services/courier/stage-update/'.$id);

	}

	public function upongoing(Request $request){
		$id = $request->input('id');

		// Update in Courier
		$courier = Courier::find($id);

		// update in Courier Service Details
		$courier_sd = CourierDetails::where('courier_id','=',$id)->first();

		// Update in Courier Status
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();

		if($request->cstatus == 0){
			$courier_st->ongoing = 1;
			$courier_st->stage = 'Delivered';
			$courier_st->save();

			// Update in Curier Details
			$courier_sd->receiver_name = $request->receiver_name;
			$courier_sd->received_data = carbon::now();
		}

		
		$courier_sd->cstat = $request->input('cstatus') ? $request->input('cstatus'):'';
		$courier_sd->save();

		Session::flash('success','Courier Ongoing updated!');
		return redirect('services/courier/stage-update/'.$id);

	}

	public function upreceived(Request $request){
		$id = $request->input('id');

		// Update in Courier
		$courier = Courier::find($id);

		// Update in Courier Status
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();
		if($request->input('r_valid_pass') == 1 || $request->input('r_old_pass') == 1 || $request->input('r_driving_lic') == 1 || $request->input('r_exit_paper') == 1 || $request->input('r_photo_r') == 1 || $request->input('r_med_rep') == 1){
			$courier_st->received = 1;
			$courier_st->ongoing = 1;
			$courier_st->stage = 'Delivered';
			$courier_st->save();

		}

		// update in Courier Service Details
		$courier_sd = CourierDetails::where('courier_id','=',$id)->first();
		$courier_sd->r_valid_pass = $request->input('r_valid_pass') ? $request->input('r_valid_pass') :'';
		$courier_sd->r_old_pass = $request->input('r_old_pass') ? $request->input('r_old_pass') :'';
		$courier_sd->r_driving_lic = $request->input('r_driving_lic') ? $request->input('r_driving_lic'):''; 
		$courier_sd->r_exit_paper = $request->input('r_exit_paper') ? $request->input('r_exit_paper'):'';
		$courier_sd->r_photo_r = $request->input('r_photo_r') ? $request->input('r_photo_r'):'';
		$courier_sd->r_med_rep = $request->input('r_med_rep') ? $request->input('r_med_rep'):'';
		$courier_sd->received_by_id = Auth::user()->user_id;
		$courier_sd->received_data = Carbon::now();
		$courier_sd->receiver_name = $request->receiver_name;
		$courier_sd->save();

		
		Session::flash('success','Courier received!');
		return redirect('services/courier/stage-update/'.$id);
	}

	public function courierStage2($id){
		
		// Get Courier Info
		$courier_info = Courier::where('last_clone_id','=',$id)->first();
		// Get Courier Clone info
		$courier_cinfo = Courierclone::find($id);
		// Get Candidate Details if available
		$cand_info = Candidate::where('cand_id','=',$courier_cinfo->cand_id)->first();
		// Get Details from Service Details
		$ser_d = ServiceDetails::where('cand_id','=',$courier_cinfo->cand_id)->first();
		// Get Courier Status
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();
		// Get Visa Details
		$EmpCand = EmployeeCandidate::where('cand_id','=',$courier_cinfo->cand_id)->first();
		// Visa Details
		if(isset($EmpCand)){
			$visa_d = Employee::where('emp_id','=',$EmpCand->emp_id)->first();
		}else{
			$visa_d = '';
		}
		// Get Courier Details
		$courier_det = CourierDetails::where('courier_id','=',$id)->first();
		// Get Mofa No
		if(isset($ser_d) && $ser_d->mofa !=''){
			$mofa_d = unserialize($ser_d->mofa);
			$mofa_no = $mofa_d['mofa_no'];
		}else{
			$mofa_d = '';
			$mofa_no = '';
		}

		// Get group candidate
		$gcands = Courier::where('groupid','=',$courier_info->groupid)->where('groupid','!=','')->where('last_clone_id','!=',$id)->get();
		// dd($mofa_no);

		// Courier List
		$courier_lists = Courier::where('groupid','!=','')->where('group_status','=',0)->groupBy('groupid')->get();

		// Branch wise view
		if(Auth::user()->user_type != 1){
			$staff = Staff::where('staff_id','=',Auth::user()->user_id)->first();
			$brStaff = $staff->staff_branch;
		}else{
			$brStaff = $courier_cinfo->br_id;
		}

		if($brStaff == $courier_cinfo->br_id){
			if($courier_st->courier_order == 0 && $courier_st->received == 0 && $courier_st->delivered_at_office == 0 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.courier-order',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands,'courier_lists' => $courier_lists]);
			}
	
			if($courier_st->dispatched == 0 && $courier_st->courier_order == 1  && $courier_st->delivered_at_office == 0 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.dispatched',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}

			if($courier_st->ongoing == 0 && $courier_st->dispatched == 1 && $courier_st->courier_order == 1 && $courier_st->delivered_at_office == 0 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.ongoing',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}

			if($courier_st->delivered == 0 && $courier_st->ongoing == 1 && $courier_st->dispatched == 1 && $courier_st->courier_order == 1 && $courier_st->delivered_at_office == 0 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.delivered',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}
			// Receiver in branch
			if($courier_st->courier_order == 0 && $courier_st->received == 1 && $courier_st->delivered_at_office == 0 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.received_final',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}

			if($courier_st->hold == 1 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.hold',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands,'courier_lists' => $courier_lists]);
			}

			if($courier_st->delivered_at_office == 1 && $courier_st->courier_order == 0 && $courier_st->received == 0 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.del_at_office_final',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}

		}else{
			if($courier_st->received == 0 && $courier_st->dispatched == 1 && $courier_st->courier_order == 1 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.received',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}

			if ($courier_st->received == 0 && $courier_st->dispatched == 0 && $courier_st->hold == 0 && $courier_st->cancel == 0) {
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.received_fail',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);				
			}

			if($courier_st->received == 1 && $courier_st->dispatched == 1 && $courier_st->courier_order == 1 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.received_final',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}

			// receive view other branch
			if($courier_st->received == 1 && $courier_st->dispatched == 0 && $courier_st->courier_order == 0 && $courier_st->hold == 0 && $courier_st->cancel == 0){
				$pageConfigs = ['pageHeader' => false];
				return view('content.service.courier.received_final',['pageConfigs' => $pageConfigs,'courier_info' => $courier_cinfo,'cand_info' => $cand_info,'ser_d' => $ser_d,'courier_det' => $courier_det,'visa_d' => $visa_d,'mofa_no' => $mofa_no,'gcands' => $gcands]);
			}
		}



		


	
		
	}

	

	public function ptsave(Request $request){
		$post = Emigration::find($request->id);
		$post->can_pt_number = $request->ptnumber;
		$post->save();
	}
	public function ensave(Request $request){
		$post = Emigration::find($request->id);
		$post->en_number = $request->enno;
		$post->save();
	}

	public function pssave(Request $request){
		$post = Emigration::find($request->id);
		$post->policy_number = $request->policy_no;
		$post->save();
	}

	public function updateAddType(Request $request){
		$id = $request->id;
		$add_type = $request->value;
		$courier = Courier::where('last_clone_id','=',$id)->first();
		$courierC = Courierclone::where('id','=',$id)->first();
		$courier->add_type = $add_type;
		$courier->save();
		$courierC->add_type = $add_type;
		$courierC->save();
	}

	public function uporder2(Request $request){
		$id = $request->input('id');
		// dd($request);
		// Update in Courier
		$courier = Courierclone::find($id);
		$courierC = Courier::where('last_clone_id','=',$id)->first();

		// Group ID Count
		// $groupIDC = Courierclone::select('groupid')->groupBy('groupid')->where('groupid','!=','')->where('id','!=',$id)->get();
		// $chkgroupIDC = Courierclone::where('groupid','=',$courier->groupid)->get();
		// $countGroupid = $groupIDC->count();
		// if($chkgroupIDC->count() == 1){
		// 	$latestcount = $courier->groupid;
		// }else{
		// 	$latestcount = $countGroupid + 1;
		// }


		// Update in Courier Status
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();
		if($request->input('valid_pass') == 1 || $request->input('old_pass') == 1 || $request->input('driving_lic') == 1 || $request->input('exit_paper') == 1 || $request->input('photo_r') == 1 || $request->input('med_rep') == 1){
			
			// $courier->csn_id = $request->input('csn_id');
			// $courierC->csn_id = $request->input('csn_id');

			// if($courier->groupid != $request->groupid && $request->groupid != 'none'){
			// 	$courier->groupid = $request->groupid;
			// 	$courierC->groupid = $request->groupid;
			// }elseif($request->groupid == 'none'){
			// 	// $courier->groupid = $countGroupid + 1;
			// 	// $courierC->groupid = $countGroupid + 1;
			// 	$courier->groupid = $latestcount;
			// 	$courierC->groupid = $latestcount;
			// }
			
			
			$courier_st->courier_order = 1;
			$courier_st->stage = 'Dispatched';
			$courier_st->save();

			$courier->stage = 'Dispatched';
			$courierC->stage = 'Dispatched';

			// update in Courier Service Details
			$courier_sd = CourierDetails::where('courier_id','=',$id)->first();
			// $courier_sd->csn_id = $request->input('csn_id') ? $request->input('csn_id') :'';
			$courier_sd->valid_pass = $request->input('valid_pass') ? $request->input('valid_pass') :'';
			$courier_sd->old_pass = $request->input('old_pass') ? $request->input('old_pass') :'';
			$courier_sd->driving_lic = $request->input('driving_lic') ? $request->input('driving_lic'):''; 
			$courier_sd->exit_paper = $request->input('exit_paper') ? $request->input('exit_paper'):'';
			$courier_sd->photo_r = $request->input('photo_r') ? $request->input('photo_r'):'';
			$courier_sd->med_rep = $request->input('med_rep') ? $request->input('med_rep'):'';
			$courier_sd->medical_token = $request->input('medical_token') ? $request->input('medical_token'):'';
			$courier_sd->dispatch_by_id = Auth::user()->user_id;
			$courier_sd->dispatch_date = Carbon::now();
			$courier_sd->partyadd_id = $request->courier_to;
			// $courier_sd->groupid = $latestcount;

			$courier->save();
			$courierC->save();
			$courier_sd->save();


			// Courier Notification List
			$courier_not = new CourerNotificationList();
			$courier_not->candidate_name = $courier->cand_fullname;
			$courier_not->pass_no = $courier->pass_no;
			$courier_not->pty_id = $courier->pty_id;
			$courier_not->care_of_id = $courier->care_of_id;
			$courier_not->courier_from = $courier->courier_from;
			$courier_not->dod = Carbon::now();
			$courier_not->save();

			// Sending Details
			$getParty = Party::where('pty_id','=',$courier->pty_id)->first();
			$getUser = User::where('user_id','=',$courier->care_of_id)->first();
			// $branch = BranchDetails::where('br_id','=',$courier->courier_from)->first();

			// create JOB
			CourierNotificationDRM::dispatch($courier_not,$getParty,$getUser)->onQueue('courier');
			CourierNotificationDRW::dispatch($courier_not,$getParty,$getUser)->onQueue('courier');

			// create reminder for dispatch order
			$courrem = new CourierReminderList();
			$courrem->courier_id = $id;
			$courrem->user_id = Auth::user()->user_id;
			$courrem->stage_name = "Dispatch";
			$courrem->save();

			Session::flash('success','Courier Order Details Updated!');

		}else{
			Session::flash('error','Please select at least one document!');
		}


		// if($request->input('valid_pass') == 1 || $request->input('old_pass') == 1 || $request->input('driving_lic') == 1 || $request->input('exit_paper') == 1 || $request->input('photo_r') == 1 || $request->input('med_rep') == 1){
		// 	Session::flash('success','Courier Order Details Updated!');
		// }else{
		// 	Session::flash('error','Please select at least one document!');
		// }


		
		return redirect('services/courier/stage-update/'.$id);

	}

	public function updispatch2(Request $request){
		$id = $request->input('id');

		if($request->hasFile('courier_slip')) {
    		$file = $request->file('courier_slip');
    		$file_count = File::files(base_path().'/public/image/service-candidate');
    		$filecount = 0;
			if ($file_count !== false) {
      			$filecount = count($file_count);
    		}
    		$file_exe = $file->getClientOriginalExtension();
    		$name = $filecount . '.' . $file_exe;

    		$file->move(base_path().'/public/image/service-candidate', $name);
    		$profile_pic = $name;

	  	} else {
    		$profile_pic = '';
  		}

		

		// Update in Courier
		$courier = Courierclone::find($id);
		$courierC = Courier::where('last_clone_id','=',$id)->first();

		$courier->stage = 'Ongoing';
		$courier->csn_id = $request->csn_id;
		$courierC->stage = 'Ongoing';
		$courierC->csn_id = $request->csn_id;

		$courier->save();
		$courierC->save();


		// check group exists or not
		// $grCheck = Courierclone::where('groupid','=',$courier->groupid)->where('groupid','!=','')->get();

		// if($grCheck->count() > 0){
		// 	foreach($grCheck as $grChec){
		// 		// Update in Courier Status
		// 		$courier_st = CourierStatus::where('courier_id','=',$grChec->id)->first();

		// 		// update in Courier Service Details
		// 		$courier_sd = CourierDetails::where('courier_id','=',$grChec->id)->first();
		// 		$courier_sd->tracking_id = $request->input('tracking_id');
		// 		$courier_sd->courier_slip = $profile_pic;
		// 		$courier_sd->save();

		// 		$courier_st->dispatched = 1;
		// 		$courier_st->stage = 'Ongoing';
		// 		$courier_st->save();

		// 		// Update group status in courier table
		// 		$courierUP = Courierclone::find($grChec->id);
		// 		$courierUP->group_status = 1;
		// 		$courierUP->save();
		// 	}
		// }else{
		// 	// Update in Courier Status
		// 	$courier_st = CourierStatus::where('courier_id','=',$id)->first();

		// 	// update in Courier Service Details
		// 	$courier_sd = CourierDetails::where('courier_id','=',$id)->first();
		// 	$courier_sd->tracking_id = $request->input('tracking_id');
		// 	$courier_sd->courier_slip = $profile_pic;
		// 	$courier_sd->csn_id = $request->csn_id;
		// 	$courier_sd->save();

		// 	$courier_st->dispatched = 1;
		// 	$courier_st->stage = 'Ongoing';

		// 	// $courierC->stage = 'Ongoing';
		// 	// $courier->stage = 'Ongoing';

			
		// 	$courier_st->save();
		// }

		// temporary update status with whenever groupID development not complete?
		// Update in Courier Status
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();

		// update in Courier Service Details
		$courier_sd = CourierDetails::where('courier_id','=',$id)->first();
		$courier_sd->tracking_id = $request->input('tracking_id');
		$courier_sd->courier_slip = $profile_pic;
		$courier_sd->csn_id = $request->csn_id;
		$courier_sd->courier_select_per = $request->courier_select_per;
		$courier_sd->save();

		$courier_st->dispatched = 1;
		$courier_st->stage = 'Ongoing';

		$courier_st->save();


		// Add Courier Notification
		// $courier_not = new Couriernotification();
		// $courier_not->courier_id = $id;
		// $courier_not->pty_id = $courier->pty_id;
		// $courier_not->careoff_id = $courier->care_of_id;
		// $courier_not->csn_id = $courier->csn_id;
		// $courier_not->tracking_id = $request->input('tracking_id');
		// $courier_not->courier_slip = $profile_pic;
		// $courier_not->stage_name = 'Ongoing';
		// $courier_not->upload_date = Carbon::now();
		// $courier_not->save();

		// Add Courier Notification List
		$courier_not = new CourerNotificationList();
		$courier_not->candidate_name = $courier->cand_fullname;
		$courier_not->pass_no = $courier->pass_no;
		$courier_not->pty_id = $courier->pty_id;
		$courier_not->care_of_id = $courier->care_of_id;
		$courier_not->tracking_id = $request->input('tracking_id');
		$courier_not->file = $profile_pic;
		$courier_not->csn_id = $request->csn_id;
		$courier_not->dod = $courier_sd->dispatch_date;
		$courier_not->valid_pass = $courier_sd->valid_pass;
		$courier_not->old_pass = $courier_sd->old_pass;
		$courier_not->driving_lic = $courier_sd->driving_lic;
		$courier_not->exit_paper = $courier_sd->exit_paper;
		$courier_not->photo_r = $courier_sd->photo_r;
		$courier_not->med_rep = $courier_sd->med_rep;
		$courier_not->medical_token = $courier_sd->medical_token;
		$courier_not->save();

		// Get API Details
		$getAPI = Userwhatsappapi::where('api_for','Visa Service')->where('status','=',1)->first();
		// Get Party Details	
		$getarty = Party::where('pty_id','=',$courier->pty_id)->first();
		// Get User Details
		$getUser = User::where('user_id','=',$courier->care_of_id)->first();
		// Get courier name details
		$getCsn = CourierServiceName::where('id','=',$request->csn_id)->first();

		// Create Job
		CourierNotificationDM::dispatch($courier_not,$getAPI,$getarty,$getUser,$getCsn)->onQueue('courier');
		CourierNotificationDW::dispatch($courier_not,$getAPI,$getarty,$getUser,$getCsn)->onQueue('courier');

		// CourierNotificationDMW::dispatch($courier_not,$getAPI,$getarty,$getUser,$getCsn)->onQueue('courier');

		// Change reminder status
		$courremind = CourierReminderList::where('courier_id','=',$id)->where('stage_name','=','Dispatch')->first();
		$courremind->status = true;
		$courremind->save();

		Session::flash('success','Courier Dispatched Successfully!');
		return redirect('services/courier/stage-update/'.$id);

	}

	public function upongoing2(Request $request){
		$id = $request->input('id');

		// Update in Courier
		$courier = Courierclone::find($id);
		$courierC = Courier::where('last_clone_id','=',$id)->first();

		// update in Courier Service Details
		$courier_sd = CourierDetails::where('courier_id','=',$id)->first();

		// Update in Courier Status
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();

		if($request->cstatus == 0){
			$courier_st->ongoing = 1;
			$courier_st->stage = 'Delivered';
			$courier_st->save();

			// Update in Curier Details
			$courier_sd->receiver_name = $request->receiver_name;
			$courier_sd->received_data = carbon::now();

			// Update in Courier Clone
			$courier->status = '1';
			$courier->stage = 'Delivered';
			$courier->save();

			$courierC->stage = 'Delivered';
			$courierC->save();
		}else{
			$courier_st->stage = 'Ongoing';
			$courier_st->save();

			$courier->stage = 'Ongoing';
			$courier->save();

			$courierC->stage = 'Ongoing';
			$courierC->save();
		}

		
		$courier_sd->cstat = $request->input('cstatus') ? $request->input('cstatus'):'';
		$courier_sd->save();

		Session::flash('success','Courier Ongoing updated!');
		return redirect('services/courier/stage-update/'.$id);

	}

	public function upreceived2(Request $request){
		$id = $request->input('id');

		// Update in Courier
		$courier = Courierclone::find($id);
		$courierC = Courier::where('last_clone_id','=',$id)->first();

		// Update in Courier Status
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();
		if($request->input('r_valid_pass') == 1 || $request->input('r_old_pass') == 1 || $request->input('r_driving_lic') == 1 || $request->input('r_exit_paper') == 1 || $request->input('r_photo_r') == 1 || $request->input('r_med_rep') == 1){
			$courier_st->received = 1;
			$courier_st->ongoing = 1;
			$courier_st->stage = 'Delivered';
			$courier_st->save();

			// Update Courier Clone
			$courier->status = '1';
			$courier->stage = 'Delivered';
			$courier->save();

			$courierC->stage = 'Delivered';
			$courierC->save();
		}

		// update in Courier Service Details
		$courier_sd = CourierDetails::where('courier_id','=',$id)->first();
		$courier_sd->r_valid_pass = $request->input('r_valid_pass') ? $request->input('r_valid_pass') :'';
		$courier_sd->r_old_pass = $request->input('r_old_pass') ? $request->input('r_old_pass') :'';
		$courier_sd->r_driving_lic = $request->input('r_driving_lic') ? $request->input('r_driving_lic'):''; 
		$courier_sd->r_exit_paper = $request->input('r_exit_paper') ? $request->input('r_exit_paper'):'';
		$courier_sd->r_photo_r = $request->input('r_photo_r') ? $request->input('r_photo_r'):'';
		$courier_sd->r_med_rep = $request->input('r_med_rep') ? $request->input('r_med_rep'):'';
		$courier_sd->received_by_id = Auth::user()->user_id;
		$courier_sd->received_data = Carbon::now();
		$courier_sd->receiver_name = $request->receiver_name;
		$courier_sd->save();

		
		Session::flash('success','Courier received!');
		return redirect('services/courier/stage-update/'.$id);
	}

	public function hold2(Request $request){
		$id = $request->courier_id;
		$stage = $request->stage;

		$courier = Courier::where('last_clone_id','=',$id)->first();
		$courier_c = Courierclone::find($id);

		$courier_det = CourierDetails::where('courier_id','=',$id)->first();
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();

		$courier->stage = "Hold";
		$courier->save();

		$courier_c->stage = "Hold";
		$courier_c->save();

		$courier_st->hold = 1;
		$courier_st->stage = "Hold";
		$courier_st->save();


	}

	public function reorder2(Request $request){
		$id = $request->courier_id;
		$stage = $request->stage;

		
		$courier = Courier::where('last_clone_id','=',$id)->first();
		$courier_c = Courierclone::find($id);

		$courier_det = CourierDetails::where('courier_id','=',$id)->first();
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();

		if($courier_st->courier_order == 0 && $courier_st->dispatched == 0 && $courier_st->ongoing == 0 && $courier_st->delivered == 0){
			$courier->stage = 'Courier Order';
			$courier->save();

			$courier_c->stage = 'Courier Order';
			$courier_c->save();

			$courier_st->hold = 0;
			$courier_st->save();
		}
		if($courier_st->courier_order == 1 && $courier_st->dispatched == 0 && $courier_st->ongoing == 0 && $courier_st->delivered == 0){
			$courier->stage = 'Dispatched';
			$courier->save();

			$courier_c->stage = 'Dispatched';
			$courier_c->save();

			$courier_st->hold = 0;
			$courier_st->save();
		}
		if($courier_st->courier_order == 1 && $courier_st->dispatched == 1 && $courier_st->ongoing == 0 && $courier_st->delivered == 0){
			$courier->stage = 'Ongoing';
			$courier->save();

			$courier_c->stage = 'Ongoing';
			$courier_c->save();

			$courier_st->hold = 0;
			$courier_st->save();
		}
		if($courier_st->courier_order == 1 && $courier_st->dispatched == 1 && $courier_st->ongoing == 1 && $courier_st->delivered == 0){
			$courier->stage = 'Delivered';
			$courier->save();

			$courier_c->stage = 'Delivered';
			$courier_c->save();

			$courier_st->hold = 0;
			$courier_st->save();
		}

	}

	public function backcourier(Request $request){
		$courier_info = Courierclone::find($request->id);
		$courier_det = CourierDetails::where('courier_id','=',$request->id)->first();
		$courier_st = CourierStatus::where('courier_id','=',$request->id)->first();
		$courier_cl = Courier::where('last_clone_id','=',$request->id)->first();
		$stage = $request->stage;
		if($stage == 1){
			// updat in courier status
			$courier_st->courier_order = 0;
			$courier_st->stage = 'Courier Order';
			$courier_st->save();
			// Update in courier
			$courier_info->stage = 'Courier Order';
			$courier_info->save();
			// Update in courier clone
			$courier_cl->stage = 'Courier Order';
			$courier_cl->save();

			Session::flash('success','Your in Courier Order!');

		}
	}

	public function courier_full_del(Request $request){
		$id = $request->courier_id;
		// Courier Clone
		$courier_c = Courier::find($id);
		$courier_infos = Courierclone::where('pass_no','=',$courier_c->pass_no)->get();
		$courier_dets = CourierDetails::where('pass_no','=',$courier_c->pass_no)->get();
		$courier_sts = CourierStatus::where('pass_no','=',$courier_c->pass_no)->get();
		$courierNots = Couriernotification::where('pass_no','=',$courier_c->pass_no)->get();
		// Delete All tables
		if(isset($courier_infos)){
			foreach($courier_infos as $courier_info){
				$courier_info->delete();
			}
		}

		if(isset($courier_dets)){
			foreach($courier_dets as $courier_det){
				$courier_det->delete();
			}
		}

		if(isset($courier_sts)){
			foreach($courier_sts as $courier_st){
				$courier_st->delete();
			}
		}

		if(isset($courierNots)){
			foreach($courierNots as $courierNot){
				$courierNot->delete();
			}
		}

		$courier_c->delete();
		Session::flash('success', 'Courier deleted successfully !');
		return redirect()->back(); 

	}

	public function courier_single_del(Request $request){
		$id = $request->courier_id;
		
		$courier_info = Courierclone::find($id);
		$courier_det = CourierDetails::where('courier_id','=',$id)->first();
		$courier_st = CourierStatus::where('courier_id','=',$id)->first();
		$courier_not = Couriernotification::where('courier_id','=',$id)->first();
		$couriers = Courierclone::where('pass_no','=',$courier_info->pass_no)->where('id','!=',$id)->get();
		$courier_c = Courier::where('last_clone_id','=',$id)->first();
		if($couriers->count() > 0){
			if(isset($courier_c)){
				// change last Clone ID
				$cour = Courierclone::where('pass_no','=',$courier_info->pass_no)->where('id','!=',$id)->first();
				$couriCU = Courier::where('pass_no','=',$courier_info->pass_no)->first();
				$couriCU->last_clone_id = $cour->id;
				$couriCU->save();
				// Delete Courier Info
				if(isset($courier_info)){
					$courier_info->delete();
				}
				if(isset($courier_det)){
					$courier_det->delete();
				}
				if(isset($courier_st)){
					$courier_st->delete();
				}
				if(isset($courier_not)){
					$courier_not->delete();
				}
				Session::flash('success', 'Courier deleted successfully !');
				return redirect()->back(); 
			}else{
				// Delete Courier Info
				if(isset($courier_info)){
					$courier_info->delete();
				}
				if(isset($courier_det)){
					$courier_det->delete();
				}
				if(isset($courier_st)){
					$courier_st->delete();
				}
				if(isset($courier_not)){
					$courier_not->delete();
				}

				Session::flash('success', 'Courier deleted successfully !');
				return redirect()->back();
			}


		}else{
			if(isset($courier_info)){
				$courier_info->delete();
			}
			if(isset($courier_det)){
				$courier_det->delete();
			}
			if(isset($courier_st)){
				$courier_st->delete();
			}
			if(isset($courier_not)){
				$courier_not->delete();
			}
			if(isset($courier_c)){
				$courier_c->delete();
			}
			Session::flash('success', 'Courier deleted successfully !');
			return	redirect('services/courier/list');
		}
	}
	// Couries Section End
}