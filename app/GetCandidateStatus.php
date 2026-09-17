<?php
namespace App;

use App\User;
use App\AdminModel\Staff;
use App\AdminModel\Lead;
use App\AdminModel\City;
use App\AdminModel\Country;
use App\AdminModel\Note;
use App\AdminModel\Task;
use App\AdminModel\Profession;
use App\AdminModel\Nationality;
use App\AdminModel\ReligionCast;
use App\AdminModel\Appoint;
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
use DB;


class GetCandidateStatus 
{
   public function get_status($id){
        global $done ;

      $cand_info = DB::table('qr_candidate_tbl as cand')
            ->leftjoin('qr_services_details as ser_det','ser_det.cand_id','=','cand.cand_id')
            ->leftjoin('qr_party_tbl as pty','pty.pty_id','=','cand.pty_id')
            ->leftjoin('qr_profession_tbl as prof','prof.prof_id','=','cand.prof_id')
            ->leftjoin('qr_nationality_tbl as nat','nat.nat_id','=','cand.nat_id')
            ->leftjoin('qr_religion_cast_tbl as rel','rel.rel_cst_id','=','cand.rel_cst_id')
            ->leftjoin('qr_employee_candidate as emp_cand','emp_cand.cand_id','=','cand.cand_id')
            ->leftjoin('qr_employee_tbl as emp_data','emp_data.emp_id','=','emp_cand.emp_id')
            ->select('cand.*','ser_det.*','rel.rel_cst_name as religion','nat.nat_name as nationality','prof.prof_eng_name as profession','emp_cand.cand_id as emp_ass_chk','pty.pty_full_name as party_name','pty.pty_ag_name as agency_name','emp_data.emp_visa_no','emp_data.spon_nm_eng','emp_data.profession as emp_profession')
            ->where('cand.cand_id','=',$id)
            ->first();

      $pass_data = Candidate::where('cand_id', '=', $id)->first();
      $pass_no = $pass_data->cand_passport_no;
      $cand_ser = CandidateService::where('cand_id', $pass_data->cand_id)->first();
      if($cand_ser){
         $cand_cat = explode(",", $cand_ser->cand_service_category);
      }

      $party_data = Party::where('pty_id', $pass_data->pty_id)->first();
      $mofa_d = ServiceDetails::where('cand_passport_no', $pass_no)->first();
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

         $st_data = ServiceStatus::where('cand_passport_no', $pass_no)->first();

         if ($st_data->cancel == '1' || $st_data->work_done == '1' ) 
         {
            $done='1';

         }
         if(in_array('emig', $cand_cat) && in_array('air_ticket', $cand_cat))
         {
            if($st_data->emi_done == '1' && $st_data->flight_book == '1')
            {
               $done='1';
            } 
         }
         else if($st_data->emi_done == '1' && !in_array('air_ticket', $cand_cat)){
            $done='1';

         }
         else if($st_data->flight_book == '1'){
            $done='1';

         }


         else if(!in_array('emig', $cand_cat) && !in_array('air_ticket', $cand_cat) && ($cand_ser->cand_visa_type)){
            $visa='1';
            if($visa==1 && $st_data->submitted == '1')
            {
               $done='1';
            }

         }

         if($done=='1')
         {
            return "Completed";
         }


         if ($st_data->flight_book == '0' && $st_data->flight_book_requied == '1' && $st_data->rejected == '0' && $st_data->submitted == '1' && $st_data->hold == '0' && $st_data->submission == '1' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_copy == '0' && $st_data->mofa == '1' && in_array("air_ticket", $cand_cat)) {
             
             return "Air ticket";

         }
         if ($st_data->emi_reject == '1' &&  $st_data->emi_done == '0' && $st_data->emi_reject_next == '0' && $st_data->emi_apply == '1' && $st_data->rejected == '0' && $st_data->submitted == '1' && $st_data->hold == '0' && $st_data->submission == '1' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1' && in_array("emig", $cand_cat)) {
             
             return "Emigration rejected";
         }

         if ($st_data->emi_done == '0' && $st_data->emi_apply == '1' && $st_data->rejected == '0' && $st_data->emi_reject_next == '0' && $st_data->submitted == '1' && $st_data->hold == '0' && $st_data->submission == '1' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1' && in_array("emig", $cand_cat)) {
             
             return "In emigration";

         }

         if ($st_data->emi_apply == '0'  && $st_data->rejected == '0' && $st_data->submitted == '1' && $st_data->hold == '0' && $st_data->submission == '1' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1' && in_array("emig", $cand_cat)) {
           
            return "For emigration";
         }

         if ($st_data->rejected == '1' && $st_data->reject_save_next == '0') {
             return "Rejected";
         }

         if ($st_data->submitted == '0' && $st_data->submission == '1'  && $st_data->hold == '0'  && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1') {

             return "Submitted";
         }

         if ($st_data->hold == '1') {
             return "Hold";
         }

         if ($st_data->submission == '0' && $st_data->hold == '0' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' &&  $st_data->ass_candidate_to_employer == '1' && $st_data->mofa == '1' ) {

             return "Ready for submission";
         }

         if ($st_data->submission == '0' && $st_data->hold == '0' && $st_data->awaited_passport == '1' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->ass_candidate_to_employer == '0' && $st_data->mofa == '1' ) {

            return "Assign candidate to employee";
         }

         if ($st_data->awaited_passport == '0' && $st_data->medicle == '1' && $st_data->medicle_online_update == '0' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1') {

            return "Awaited passport";
         }

         if ($st_data->medicle_online_update == '1' && $st_data->medicle_online_required == '1' && $st_data->medicle == '1' && $st_data->mofa_paid == '1' && $st_data->passport_original == '1' && $st_data->mofa == '1') {

           return "Medicle online update";
         }
         if ($st_data->medicle == '0' && $st_data->mofa_paid == '1'  && $st_data->passport_original == '1'  && $st_data->mofa == '1') {

            return "Medicle";
         }



         if (($st_data->mofa == '1' && $st_data->mofa_unpaid == '1' && $st_data->new_document == '1')) {
           return "Mofa unpaid";
         }

         if (($st_data->mofa == '1' && $st_data->passport_copy == '1' && $st_data->new_document == '1')) {
           return "Passport copy";
         }



         if (($st_data->mofa == '0' && $st_data->new_document == '1')) {

           return "Mofa";
         }

         if (($st_data->new_document == '0')) {

           return "New document";
         }

         if (!isset($cand_cat)) {
           return "None";

         }
            
      } else {
        return "None";
      }
   }
}